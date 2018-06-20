CREATE TABLE users (
  email VARCHAR(255) NOT NULL PRIMARY KEY,
  password VARCHAR(255) NOT NULL,
  departure VARCHAR(255),
  destination VARCHAR(255),
  seats INTEGER
);


INSERT INTO users VALUES ("u1@p.it", md5("p1"), "AA", "DD", 2);
INSERT INTO users VALUES ("u2@p.it", md5("p2"), "BB", "EE", 1);
INSERT INTO users VALUES ("u3@p.it", md5("p3"), "DD", "EE", 1);
INSERT INTO users VALUES ("u4@p.it", md5("p4"), NULL, NULL, 0);