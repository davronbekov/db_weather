from matplotlib import image
import png
import numpy
import urllib.request
from PIL import Image
import multiprocessing as mp
import mysql.connector
import json
from urllib.request import urlopen


def load_config(name):
    file = open(name, 'r')
    config = json.loads(file.read())
    file.close()
    return config


add_country = "INSERT INTO country(country_name) VALUES (%s) ON DUPLICATE KEY UPDATE country_name=country_name"

add_polygon = ("INSERT INTO country_polygons(lat, lng, country_polygon_index, country_name) "
               "VALUES (%s, %s, %s, %s)   "
               "ON DUPLICATE KEY UPDATE country_name=country_name")


def create_country(mysql_cnx, name):
    cursor = mysql_cnx.cursor()
    cursor.execute(add_country, [name])
    mysql_cnx.commit()


def create_country_polygons(mysql_cnx, name, polygons):
    cursor = mysql_cnx.cursor()
    data = list(map(lambda x: [x[1][0], x[1][1], x[0], name], enumerate(polygons)))
    cursor.executemany(add_polygon, data)
    mysql_cnx.commit()


def find_uzb(features):
    for (i, item) in enumerate(features):
        if item["properties"]["ADMIN"] == "Uzbekistan":
            return item


with open('countries.geojson', 'r') as file:
    config = load_config("db_config.json")
    data = file.read()
    countries = json.loads(data)
    try:
        mysql_cnx = mysql.connector.connect(**config)
        feature = find_uzb(countries["features"])
        country_polygons = feature["geometry"]["coordinates"][2][0]
        country_name = feature["properties"]["ADMIN"]
        create_country(mysql_cnx, country_name)
        create_country_polygons(mysql_cnx, country_name, country_polygons)
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        mysql_cnx.close()
