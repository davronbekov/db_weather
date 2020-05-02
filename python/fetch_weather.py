import datetime
import multiprocessing as mp
import mysql.connector
import numpy
import string
from PIL import Image
import os
import errno
from PIL import ImageColor

config = {
    'user': 'root',
    'password': 'secret',
    'host': '127.0.0.1',
    'database': 'db_weather',
    'raise_on_warnings': True
}


def open_image(url, mode="RGB"):
    img = Image.open(url).convert(mode)
    return numpy.asarray(img)


def save_image(img_data, file_name):
    Image.fromarray(img_data).save(file_name)


def is_valid_range_color(diff_arr):
    (grad_diff, color_diff) = diff_arr
    if grad_diff > 0 and color_diff > 0:
        return grad_diff >= color_diff
    elif grad_diff < 0 and color_diff < 0:
        return grad_diff <= color_diff
    else:
        diff = abs(grad_diff - color_diff)
        return diff <= 3 or color_diff == 0


def is_valid_range_value(grad_diff_value, diff_value):
    if grad_diff_value > 0 and diff_value > 0:
        return grad_diff_value >= diff_value
    elif grad_diff_value < 0 and diff_value < 0:
        return grad_diff_value <= diff_value
    else:
        diff = abs(grad_diff_value - diff_value)
        return diff <= 3 or diff_value == 0


def find_value_from_color(color, gradient):
    color = numpy.array(color)
    size = len(gradient)
    for index, [grad_value, grad_color] in enumerate(gradient):
        grad_color = numpy.array(grad_color)
        if (index + 1) < size:
            [next_value, next_color] = gradient[index + 1]
            next_color = numpy.array(next_color)

            grad_diff_color = next_color - grad_color
            diff_color = color - grad_color
            # zip
            is_valid = all(map(is_valid_range_color, numpy.column_stack((grad_diff_color, diff_color))))
            if is_valid:
                max_index = numpy.argmax(numpy.absolute(grad_diff_color))
                if grad_diff_color[max_index] == 0:
                    return grad_value
                else:
                    return grad_value + diff_color[max_index] / grad_diff_color[max_index] * (next_value - grad_value)
        else:
            return grad_value


def find_color_from_value(value, gradient):
    size = len(gradient)
    for index, [grad_value, grad_color] in enumerate(gradient):
        grad_color = numpy.array(grad_color)
        if (index + 1) < size:
            [next_value, next_color] = gradient[index + 1]
            next_color = numpy.array(next_color)
            grad_diff_value = next_value - grad_value
            diff_value = value - grad_value
            # zip
            is_valid = is_valid_range_value(grad_diff_value, diff_value)
            if is_valid:
                if grad_diff_value == 0:
                    return grad_color
                else:
                    return grad_color + diff_value / grad_diff_value * (next_color - grad_color)
        else:
            print("Reached max(")
            return grad_value


custom_gradient = [
    [-40, [130, 22, 146]],
    [-30, [130, 87, 219]],
    [-20, [32, 140, 236]],
    [-10, [32, 196, 232]],
    [0, [35, 221, 221]],
    [10, [194, 255, 40]],
    [20, [255, 240, 40]],
    [25, [255, 194, 40]],
    [30, [252, 128, 20]],
]


def pixel_raw(raw):
    return list(map((lambda pixel: find_value_from_color(pixel[:3], custom_gradient)), raw))


def value_raw(raw):
    return list(map((lambda pixel: find_color_from_value(pixel, custom_gradient)), raw))


add_weather_value = "INSERT INTO weather_value(wkey, value, weather_id) "\
                     "VALUES (%s, %s, %s)   "


def create_weather_value(mysql_cnx, wkey, value, weather_id):
    cursor = mysql_cnx.cursor()
    cursor.execute(add_weather_value, [wkey, value, weather_id])
    mysql_cnx.commit()


add_weather = ("INSERT INTO weather values()")

get_color_mapping = "SELECT * FROM color_mapping where map_id = %s"


def select_color_mapping(mysql_cnx, map_id):
    cursor = mysql_cnx.cursor()
    cursor.execute(get_color_mapping, [map_id])
    return list(map(lambda x: {"color": list(ImageColor.getrgb(x[0])),
                               "value": x[1],
                               "map_id": x[2],
                               }, cursor.fetchall()))


def create_weather(mysql_cnx):
    cursor = mysql_cnx.cursor()
    cursor.execute(add_weather)
    mysql_cnx.commit()
    return cursor.lastrowid


add_map_pixel = ("INSERT INTO map_pixel(tile_coordinate_x, tile_coordinate_y, tile_coordinate_z, timestamp, map_id, "
                 "pixel_coordinate_x, pixel_coordinate_y, world_coordinate_lng, world_coordinate_lat, weather_id) "
                 "VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)   ")


def create_map_pixel(mysql_cnx, map_tile, map_pixel):
    cursor = mysql_cnx.cursor()
    cursor.execute(add_map_pixel, [map_tile["x"],
                                   map_tile["y"],
                                   map_tile["z"],
                                   map_tile["timestamp"],
                                   map_tile["map_id"],
                                   map_pixel["pixel_x"],
                                   map_pixel["pixel_y"],
                                   map_pixel["world_coordinate_lng"],
                                   map_pixel["world_coordinate_lat"],
                                   map_pixel["weather_id"]])
    mysql_cnx.commit()


def store_tile_pixel(mysql_cnx, tile, pixel):
    id = create_weather(mysql_cnx)
    color_mapping = select_color_mapping(mysql_cnx, tile["map_id"])

    value = find_value_from_color(pixel["color"][:3], list(map((lambda x: [x["value"], x["color"][:3]]), color_mapping)))

    create_weather_value(mysql_cnx, tile["map_id"], int(value), id)
    pixel["world_coordinate_lng"] = 0
    pixel["world_coordinate_lat"] = 0
    pixel["weather_id"] = id
    create_map_pixel(mysql_cnx, tile, pixel)



def map_tile_pixels_fn(mysql_cnx, tile, pixel_y):
    return lambda pixel: store_tile_pixel(mysql_cnx, tile,
                                          {"color": pixel[1],
                                           "pixel_x": pixel[0],
                                           "pixel_y": pixel_y})


def map_tile_row(mysql_cnx, tile, row):
    return list(map(map_tile_pixels_fn(mysql_cnx, tile, row[0]), enumerate(row[1])))


def map_tile_row_fn(mysql_cnx, tile):
    return lambda row: map_tile_row(mysql_cnx, tile, row)


get_not_computed_map_tiles = "select * from map_tile " \
                             "join map m on map_tile.map_id = m.map_id " \
                             "where (tile_coordinate_x, tile_coordinate_y, tile_coordinate_z, timestamp, m.map_id) " \
                             "not in (select map_tile.tile_coordinate_x, map_tile.tile_coordinate_y, map_tile.tile_coordinate_z, map_tile.timestamp, map_tile.map_id from map_tile " \
                             "join map_pixel mp on map_tile.tile_coordinate_x = mp.tile_coordinate_x and map_tile.tile_coordinate_y = mp.tile_coordinate_y and map_tile.tile_coordinate_z = mp.tile_coordinate_z and map_tile.timestamp = mp.timestamp and map_tile.map_id = mp.map_id)" \
                             "and m.is_regular"


def select_not_computed_map_tiles(mysql_cnx):
    cursor = mysql_cnx.cursor()
    cursor.execute(get_not_computed_map_tiles)
    return list(map(lambda x: {"x": x[0],
                               "y": x[1],
                               "z": x[2],
                               "timestamp": x[3],
                               "image": x[4],
                               "map_id": x[5],
                               }, cursor.fetchall()))


def compute_tile(mysql_cnx, tile):
    data = open_image('../' + tile["image"], "RGBA")
    list(map(map_tile_row_fn(mysql_cnx, tile), enumerate(data.tolist())))
    # with mp.Pool(processes=6) as pool:
    #     # result = numpy.asarray(pool.map(pixel_raw, data))
    #
    #
    #     # new_img = numpy.asarray(pool.map(value_raw, result), dtype=numpy.uint8)
    #     # save_image(new_img, "3_output.png")


def compute_weather():
    try:
        mysql_cnx = mysql.connector.connect(**config)
        now = datetime.datetime.now()
        for map_tile in select_not_computed_map_tiles(mysql_cnx):
            compute_tile(mysql_cnx, map_tile)
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        mysql_cnx.close()


if __name__ == '__main__':
    compute_weather()
