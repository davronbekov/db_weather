
CREATE TABLE City
(
	city_name            VARCHAR(255) NOT NULL,
	login                CHAR(18) NOT NULL,
	country_name         VARCHAR(255) NOT NULL
);

ALTER TABLE City
ADD CONSTRAINT XPKCity PRIMARY KEY (city_name,country_name);

CREATE TABLE City_Polygons
(
	x                    INTEGER NULL,
	y                    INTEGER NULL,
	city_polygon_index   INTEGER NOT NULL,
	city_name            VARCHAR(255) NOT NULL,
	country_name         VARCHAR(255) NOT NULL
);

ALTER TABLE City_Polygons
ADD CONSTRAINT XPKCity_Polygons PRIMARY KEY (city_polygon_index,city_name,country_name);

CREATE TABLE Color_Mapping
(
	color                CHAR(7) NOT NULL,
	value                FLOAT NULL,
	type                 CHAR(18) NOT NULL
);

ALTER TABLE Color_Mapping
ADD CONSTRAINT XPKColor_Mapping PRIMARY KEY (color,type);

CREATE TABLE Country
(
	country_name         VARCHAR(255) NOT NULL
);

ALTER TABLE Country
ADD CONSTRAINT XPKCountry PRIMARY KEY (country_name);

CREATE TABLE Country_Polygons
(
	x                    INTEGER NULL,
	y                    INTEGER NULL,
	country_polygon_index VARCHAR(255) NOT NULL,
	country_name         VARCHAR(255) NOT NULL
);

ALTER TABLE Country_Polygons
ADD CONSTRAINT XPKCountry_Polygons PRIMARY KEY (country_polygon_index,country_name);

CREATE TABLE Map_Tile
(
	tile_coordinate_x    INTEGER NOT NULL,
	tile_coordinate_y    INTEGER NOT NULL,
	tile_coordinate_z    INTEGER NOT NULL,
	timestamp            TIMESTAMP NOT NULL,
	image                VARCHAR(255) NULL,
	type                 CHAR(18) NOT NULL
);

ALTER TABLE Map_Tile
ADD CONSTRAINT XPKMap_Tile PRIMARY KEY (tile_coordinate_x,tile_coordinate_y,tile_coordinate_z,timestamp,type);

CREATE TABLE Map_Type
(
	type                 CHAR(18) NOT NULL
);

ALTER TABLE Map_Type
ADD CONSTRAINT XPKMap_Type PRIMARY KEY (type);

CREATE TABLE User
(
	login                CHAR(18) NOT NULL,
	password             CHAR(18) NULL,
	scope                VARCHAR(255) NULL,
	email                VARCHAR(255) NULL
);

ALTER TABLE User
ADD CONSTRAINT XPKUser PRIMARY KEY (login);

CREATE TABLE Weather_Condition
(
	type                 CHAR(18) NOT NULL,
	icon                 VARCHAR(255) NULL,
	description          VARCHAR(255) NULL
);

ALTER TABLE Weather_Condition
ADD CONSTRAINT XPKWeather_Condition PRIMARY KEY (type);

CREATE TABLE Weather_History
(
	pixel_coordinate_x   INTEGER NOT NULL,
	pixel_coordinate_y   INTEGER NOT NULL,
	pixel_coordinate_z   INTEGER NOT NULL,
	world_coordinate_lng FLOAT NULL,
	world_coordinate_lat FLOAT NULL,
	temperature          FLOAT NULL,
	pressure             FLOAT NULL,
	precipation          FLOAT NULL,
	wind_speed           FLOAT NULL,
	clouds               FLOAT NULL,
	tile_coordinate_x    INTEGER NOT NULL,
	tile_coordinate_y    INTEGER NOT NULL,
	tile_coordinate_z    INTEGER NOT NULL,
	timestamp            TIMESTAMP NOT NULL,
	type                 CHAR(18) NOT NULL,
	city_name            VARCHAR(255) NOT NULL,
	country_name         VARCHAR(255) NOT NULL
);

ALTER TABLE Weather_History
ADD CONSTRAINT XPKWeather_History PRIMARY KEY (pixel_coordinate_x,pixel_coordinate_y,pixel_coordinate_z,tile_coordinate_x,tile_coordinate_y,tile_coordinate_z,timestamp,type);

ALTER TABLE City
ADD CONSTRAINT live_in FOREIGN KEY (login) REFERENCES User (login);

ALTER TABLE City
ADD CONSTRAINT belongs_to FOREIGN KEY (country_name) REFERENCES Country (country_name);

ALTER TABLE City_Polygons
ADD CONSTRAINT bounded_by_1 FOREIGN KEY (city_name, country_name) REFERENCES City (city_name, country_name);

ALTER TABLE Color_Mapping
ADD CONSTRAINT ranged_by FOREIGN KEY (type) REFERENCES Map_Type (type);

ALTER TABLE Country_Polygons
ADD CONSTRAINT bounded_by_2 FOREIGN KEY (country_name) REFERENCES Country (country_name);

ALTER TABLE Map_Tile
ADD CONSTRAINT define FOREIGN KEY (type) REFERENCES Map_Type (type);

ALTER TABLE Weather_History
ADD CONSTRAINT depict FOREIGN KEY (tile_coordinate_x, tile_coordinate_y, tile_coordinate_z, timestamp, type) REFERENCES Map_Tile (tile_coordinate_x, tile_coordinate_y, tile_coordinate_z, timestamp, type);

ALTER TABLE Weather_History
ADD CONSTRAINT is_described_by FOREIGN KEY (type) REFERENCES Weather_Condition (type);

ALTER TABLE Weather_History
ADD CONSTRAINT has FOREIGN KEY (city_name, country_name) REFERENCES City (city_name, country_name);
