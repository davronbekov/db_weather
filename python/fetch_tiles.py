import datetime
import requests
import os
import errno
import mysql.connector
import math
import numpy

# for uzbekistan
# (min_x, min_y, max_x, max_y) = 335, 182, 361, 200
min_latlng = numpy.asarray([45.833861, 55.710446])
max_latlng = numpy.asarray([36.849236, 73.398434])


def latlng2pixel_xy(lat_lng, zoom):
    (lat, lng) = lat_lng
    size = pow(2, zoom) * 256
    x = (lng + 180) * (size / 360)
    lat_rad = math.radians(lat)
    merc_n = math.log(math.tan(math.pi / 4 + lat_rad / 2))
    y = 1 / 2 * size * (1 - merc_n / math.pi)
    return numpy.asarray([x, y])


config = {
    'user': 'root',
    'password': 'secret',
    'host': '127.0.0.1',
    'database': 'db_weather',
    'raise_on_warnings': True
}

get_country_box = "select min((lng + 180) * (POWER(2, 9) * 256 / 360)) / 256                                   as min_x," \
                  "       min(power(2, 9) * 256 / 2 * (1 - ln(tan(pi() / 4 + RADIANS(lat) / 2)) / PI())) / 256 as min_y," \
                  "       max((lng + 180) * (POWER(2, 9) * 256 / 360)) / 256                                   as max_x," \
                  "       max(power(2, 9) * 256 / 2 * (1 - ln(tan(pi() / 4 + RADIANS(lat) / 2)) / PI())) / 256 as max_y," \
                  "from country_polygons" \
                  "where country_name = %s;"


def download_image(url, file_name):
    myfile = requests.get(url)
    if not os.path.exists(os.path.dirname(file_name)):
        try:
            os.makedirs(os.path.dirname(file_name))
        except OSError as exc:  # Guard against race condition
            if exc.errno != errno.EEXIST:
                raise

    open(file_name, 'wb').write(myfile.content)
    return file_name


def download_tiles(time, service_map, zoom):
    (min_x, min_y) = latlng2pixel_xy(min_latlng, zoom) / 256
    (max_x, max_y) = latlng2pixel_xy(max_latlng, zoom) / 256 + 1
    saved_images = []
    z = zoom
    print("Starting to download", service_map["map_id"], " with zoom = ", zoom)
    for x in range(int(min_x), int(max_x)):
        for y in range(int(min_y), int(max_y)):
            file_path = "tiles/{0}/{1}/{5}/{2}/{3}_{4}.png".format(
                service_map['service_name'], service_map['map_id'],
                time.strftime("%d%m%Y_%H%M%S"), x, y, z)
            print("Downloading... ", service_map['params_url'].format(service_map["map_id"], x, y, z))
            download_image(service_map['params_url'].format(service_map["map_id"], x, y, z), "../" + file_path)
            saved_images.append({"image": file_path,
                                 "x": x,
                                 "y": y,
                                 "z": z,
                                 "map_id": service_map['map_id'],
                                 "timestamp": str(time.strftime("%Y-%m-%d %H:%M:%S"))})
            print("Downloaded ", service_map['params_url'].format(service_map["map_id"], x, y, z))
    print("End of download ", service_map["map_id"], " with zoom = ", zoom)
    return saved_images


get_service_map = "SELECT M.MAP_ID AS MAP_ID,  S.NAME AS SERVICE_NAME, S.PARAMS_URL AS PARAMS_URL " \
                  "FROM PROVIDES " \
                  "LEFT JOIN MAP M ON PROVIDES.MAP_ID = M.MAP_ID " \
                  "LEFT JOIN SERVICE S ON PROVIDES.NAME = S.NAME " \
                  "WHERE M.IS_REGULAR = %s"

add_map_tile = ("INSERT INTO map_tile( tile_coordinate_x, tile_coordinate_y, tile_coordinate_z, timestamp, image, map_id) "
                "VALUES (%s, %s, %s, %s,%s, %s)   ")


def select_service_map(mysql_cnx, is_regular):
    cursor = mysql_cnx.cursor()
    cursor.execute(get_service_map, [is_regular])

    return list(map(lambda x: {"params_url": x[2],
                               "map_id": x[0],
                               "service_name": x[1]
                               }, cursor.fetchall()))


def create_map_tiles(mysql_cnx, data):
    cursor = mysql_cnx.cursor()
    cursor.executemany(add_map_tile, data)
    mysql_cnx.commit()


def fetch_tiles(regular, zoom):
    try:
        mysql_cnx = mysql.connector.connect(**config)
        now = datetime.datetime.now()
        for service_map in select_service_map(mysql_cnx, regular):
            saved_tiles = download_tiles(now, service_map, zoom)
            create_map_tiles(mysql_cnx, map(lambda x: [x["x"],
                                                       x["y"],
                                                       x["z"],
                                                       x["timestamp"],
                                                       x["image"],
                                                       x["map_id"]], saved_tiles))

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
    for zoom in range(6,8):
        fetch_tiles(0, zoom)
        fetch_tiles(1, zoom)
# fetch_tiles(0)
