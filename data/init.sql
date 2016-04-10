PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS img;
DROP TABLE IF EXISTS comment;


CREATE TABLE user (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  username VARCHAR NOT NULL,
  password VARCHAR NOT NULL
);

CREATE TABLE img (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  cdate VARCHAR NOT NULL,
  title VARCHAR NOT NULL,
  author INTEGER NOT NULL,
  ipath VARCHAR NOT NULL,
  FOREIGN KEY (author) REFERENCES user(id)
);

CREATE TABLE comment (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  img INTEGER NOT NULL,
  cdate VARCHAR NOT NULL,
  name VARCHAR NOT NULL,
  body VARCHAR NOT NULL,
  FOREIGN KEY (img) REFERENCES img(id)
);