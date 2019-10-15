DROP TABLE IF EXISTS Selling;
DROP TABLE IF EXISTS Vote;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Paid;
DROP TABLE IF EXISTS Purchase;
DROP TABLE IF EXISTS PaymentMethod;
DROP TABLE IF EXISTS Cart;
DROP TABLE IF EXISTS Favorite;
DROP TABLE IF EXISTS ComplementaryImage;
DROP TABLE IF EXISTS GameCategory;
DROP TABLE IF EXISTS Category;
DROP TABLE IF EXISTS Game;
DROP TABLE IF EXISTS Developer;
DROP TABLE IF EXISTS BannedUser;
DROP TABLE IF EXISTS Seller;
DROP TABLE IF EXISTS NormalUser;
DROP TABLE IF EXISTS "User";
DROP TABLE IF EXISTS Administrator;
DROP TABLE IF EXISTS Country;
DROP TABLE IF EXISTS Image;
DROP TABLE IF EXISTS password_resets CASCADE;

DROP TYPE IF EXISTS states;
DROP TYPE IF EXISTS genders;

DROP INDEX IF EXISTS user_purchase;
DROP INDEX IF EXISTS developer_game;
DROP INDEX IF EXISTS search_game_idx;
DROP INDEX IF EXISTS search_user_idx;
DROP INDEX IF EXISTS password_resets_email_index;
DROP INDEX IF EXISTS password_resets_token_index;

DROP FUNCTION IF EXISTS AfterDeleteUser();
DROP TRIGGER IF EXISTS AfterDeleteUser ON NormalUser;
DROP FUNCTION IF EXISTS AfterDeleteReview();
DROP TRIGGER IF EXISTS AfterDeleteReview ON Review;
DROP FUNCTION IF EXISTS AfterGamePurchase();
DROP TRIGGER IF EXISTS AfterGamePurchase ON Paid;
DROP FUNCTION IF EXISTS UpdateGameScore();
DROP TRIGGER IF EXISTS UpdateGameScore ON Review;
DROP FUNCTION IF EXISTS UpdateGameScoreOnDelete();
DROP TRIGGER IF EXISTS UpdateGameScoreOnDelete ON Review;
DROP TRIGGER IF EXISTS UpdateGameScoreOnUpdate ON Review;
DROP FUNCTION IF EXISTS UpdateTotalPaid();
DROP TRIGGER IF EXISTS UpdateTotalPaid ON Paid;
DROP FUNCTION IF EXISTS BeforeInsertCart();
DROP TRIGGER IF EXISTS BeforeInsertCart ON Cart;
DROP FUNCTION IF EXISTS BeforeInsertVote();
DROP TRIGGER IF EXISTS BeforeInsertVote ON Vote;
DROP FUNCTION IF EXISTS AfterInsertVote();
DROP TRIGGER IF EXISTS AfterInsertVote ON Vote;
DROP FUNCTION IF EXISTS AfterDeleteVote();
DROP TRIGGER IF EXISTS AfterDeleteVote ON Vote;

CREATE TYPE states AS ENUM('Pending', 'Accepted', 'Rejected', 'Deleted');
CREATE TYPE genders AS ENUM('Female', 'Male', 'Other');

CREATE TABLE Country (
  id          SERIAL PRIMARY KEY,
  name        TEXT NOT NULL UNIQUE
);

CREATE TABLE Image (
  id          SERIAL PRIMARY KEY,
  "path"        TEXT NOT NULL
);

CREATE TABLE Administrator (
  id          SERIAL PRIMARY KEY,
  username      TEXT NOT NULL UNIQUE,
  password      TEXT NOT NULL,
  remember_token VARCHAR
);

CREATE TABLE "User" (
  id          SERIAL PRIMARY KEY,
  username      TEXT UNIQUE,
  password      TEXT NOT NULL,
  email         TEXT UNIQUE,
  profilePicture    INTEGER REFERENCES Image (id) ON UPDATE CASCADE,
  remember_token VARCHAR
);

CREATE TABLE NormalUser (
  id          INTEGER PRIMARY KEY REFERENCES "User" (id) ON UPDATE CASCADE,
  name        TEXT,
  idCountry     INTEGER REFERENCES Country (id) ON UPDATE CASCADE ON DELETE SET NULL,
  gender        genders,
  joindate      TIMESTAMP WITH TIME zone DEFAULT now(),
  birthdate     DATE
);

CREATE TABLE Seller (
  id          INTEGER PRIMARY KEY REFERENCES "User" (id) ON UPDATE CASCADE
);

CREATE TABLE BannedUser (
  id          INTEGER PRIMARY KEY REFERENCES NormalUser (id) ON UPDATE CASCADE ON DELETE CASCADE,
  reason        TEXT NOT NULL
);

CREATE TABLE Developer (
  id          SERIAL PRIMARY KEY,
  pen_name      TEXT NOT NULL UNIQUE,
  company       TEXT,
  url       TEXT
);

CREATE TABLE Game (
  id          SERIAL PRIMARY KEY,
  name        TEXT NOT NULL UNIQUE,
  description     TEXT NOT NULL,
  idDeveloper     INTEGER REFERENCES Developer (id) ON UPDATE CASCADE,
  price       REAL NOT NULL CONSTRAINT price_ck CHECK (price >= 0),
  briefDescription  TEXT NOT NULL,
  score       REAL NOT NULL CONSTRAINT score_ck CHECK (score >= 0 AND score <= 5),
  "path"        TEXT NOT NULL,
  ageRestriction    INTEGER NOT NULL CONSTRAINT age_restriction_ck CHECK (ageRestriction in (0,3,7,12,16,18)),
  state       states NOT NULL DEFAULT 'Pending',
  releaseDate     TIMESTAMP WITH TIME zone DEFAULT now(),
  rejectionReason   TEXT,
  cover         INTEGER NOT NULL REFERENCES Image (id) ON UPDATE CASCADE
);

CREATE TABLE Category (
  id          SERIAL PRIMARY KEY,
  name        TEXT NOT NULL UNIQUE
);


CREATE TABLE GameCategory (
  idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE,
  idCategory      INTEGER REFERENCES Category (id) ON UPDATE CASCADE ON DELETE CASCADE,
  PRIMARY KEY (idGame, idCategory)
);

CREATE TABLE ComplementaryImage (
  idImage       INTEGER REFERENCES Image (id) ON UPDATE CASCADE ON DELETE CASCADE,
  idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE ON DELETE CASCADE,
  PRIMARY KEY (idImage, idGame)
);

CREATE TABLE Favorite (
    idUser        INTEGER REFERENCES "User" (id) ON UPDATE CASCADE,
  idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE,
  PRIMARY KEY (idGame, idUser)
);

CREATE TABLE Cart (
  idUser        INTEGER REFERENCES "User" (id) ON UPDATE CASCADE,
  idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE,
  PRIMARY KEY (idGame, idUser)
);

CREATE TABLE PaymentMethod (
    id          SERIAL PRIMARY KEY,
  method        TEXT NOT NULL UNIQUE
);

CREATE TABLE Purchase (
    id          SERIAL PRIMARY KEY,
  idUser        INTEGER NOT NULL REFERENCES "User" (id) ON UPDATE CASCADE,
  purchaseDate    TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
  totalPaid     REAL NOT NULL CONSTRAINT total_paid_ck CHECK (totalPaid >= 0),
  nif         INTEGER CONSTRAINT nif_ck CHECK (nif > 0),
  idPaymentMethod   INTEGER NOT NULL REFERENCES PaymentMethod (id) ON UPDATE CASCADE
);

CREATE TABLE Paid (
    idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE,
  idPurchase      INTEGER REFERENCES Purchase (id) ON UPDATE CASCADE,
  value       REAL NOT NULL CONSTRAINT value_ck CHECK (value >= 0),
  PRIMARY KEY(idGame, idPurchase)
);

CREATE TABLE Review (
  idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE,
    idUser        INTEGER REFERENCES "User" (id) ON UPDATE CASCADE,
    content       TEXT NOT NULL,
    score       INTEGER NOT NULL CONSTRAINT score_review_ck CHECK (score >= 0 AND score <= 5),
    creationDate    TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    votes       INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (idGame, idUser)
);

CREATE TABLE Vote (
  idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE,
    idUserReview    INTEGER REFERENCES "User" (id) ON UPDATE CASCADE,
    idUserVote      INTEGER REFERENCES "User" (id) ON UPDATE CASCADE,
    type        BOOLEAN NOT NULL,
  PRIMARY KEY (idGame, idUserReview, idUserVote)
);

CREATE TABLE Selling (
  idUser        INTEGER REFERENCES "User" (id) ON UPDATE CASCADE,
  idGame        INTEGER REFERENCES Game (id) ON UPDATE CASCADE,
  releaseDate     TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
  PRIMARY KEY (idUser, idGame)
);

CREATE TABLE password_resets
(
    id SERIAL PRIMARY KEY,
    email character varying(255) COLLATE pg_catalog."default" NOT NULL,
    token character varying(255) COLLATE pg_catalog."default" NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
)
WITH (OIDS = FALSE)
TABLESPACE pg_default;

--Indexes
CREATE INDEX password_resets_email_index
    ON password_resets USING btree
    (email COLLATE pg_catalog."default")
    TABLESPACE pg_default;
CREATE INDEX password_resets_token_index
    ON password_resets USING btree
    (token COLLATE pg_catalog."default")
    TABLESPACE pg_default;


-- reference indexes
CREATE INDEX user_purchase ON Purchase USING hash(idUser);
CREATE INDEX developer_game ON Game USING hash(idDeveloper); 


-- text search indexes
CREATE INDEX search_game_idx ON Game USING GIST (to_tsvector('english', name || ' ' || description));
CREATE INDEX search_user_idx ON NormalUser USING GIST (to_tsvector('english', name));

--Triggers

CREATE FUNCTION AfterDeleteUser() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE Purchase SET nif = NULL WHERE OLD.id = idUser;
  UPDATE "User" SET username = NULL, email = NULL WHERE OLD.id = id;
  DELETE FROM Favorite WHERE OLD.id = idUser;
  DELETE FROM Cart WHERE OLD.id = idUser;
    RETURN NULL;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER AfterDeleteUser
    AFTER DELETE ON NormalUser
    FOR EACH ROW
    EXECUTE PROCEDURE AfterDeleteUser(); 

CREATE FUNCTION AfterDeleteReview() RETURNS TRIGGER AS
$BODY$
BEGIN
    DELETE FROM Vote WHERE OLD.idUser = idUserReview AND Old.idGame = Vote.idGame;
    RETURN NULL;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER AfterDeleteReview
    AFTER DELETE ON Review
    FOR EACH ROW
    EXECUTE PROCEDURE AfterDeleteReview(); 

CREATE FUNCTION AfterGamePurchase() RETURNS TRIGGER AS
$BODY$
BEGIN
    DELETE FROM Cart WHERE idGame in 
        (SELECT Paid.idGame
        FROM Purchase, Paid
        WHERE Purchase.idUser = Cart.idUser AND Paid.idPurchase = Purchase.id);
    RETURN NULL;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER AfterGamePurchase
    AFTER Insert ON Paid
    FOR EACH ROW
    EXECUTE PROCEDURE AfterGamePurchase();

CREATE FUNCTION UpdateGameScore() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE Game
    SET score = GetAverage.average_score
    FROM (SELECT AVG(score) as average_score, idGame as game_id FROM Review GROUP BY idGame) AS GetAverage
  WHERE GetAverage.game_id = Game.id AND NEW.idGame = Game.id;
  RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER UpdateGameScore
    AFTER INSERT ON Review
    FOR EACH ROW
    EXECUTE PROCEDURE UpdateGameScore();

CREATE FUNCTION UpdateGameScoreOnDelete() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE Game
    SET score = GetAverage.average_score
    FROM (SELECT AVG(score) as average_score, idGame as game_id FROM Review GROUP BY idGame) AS GetAverage
  WHERE GetAverage.game_id = Game.id AND OLD.idGame = Game.id;
  RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;


CREATE TRIGGER UpdateGameScoreOnDelete
    AFTER DELETE ON Review
    FOR EACH ROW
    EXECUTE PROCEDURE UpdateGameScoreOnDelete();

CREATE TRIGGER UpdateGameScoreOnUpdate
    AFTER UPDATE ON Review
    FOR EACH ROW
    EXECUTE PROCEDURE UpdateGameScore();

CREATE FUNCTION UpdateTotalPaid() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE Purchase
    SET totalPaid = GetSum.total
    FROM (SELECT SUM(value) as total, idPurchase FROM Paid GROUP BY idPurchase) AS GetSum
  WHERE GetSum.idPurchase = Purchase.id AND Purchase.id = NEW.idPurchase;
  RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER UpdateTotalPaid
    AFTER INSERT ON Paid
    FOR EACH ROW
    EXECUTE PROCEDURE UpdateTotalPaid();

CREATE FUNCTION BeforeInsertCart() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS 
    (SELECT * FROM Purchase, Paid 
    WHERE Paid.idPurchase = Purchase.id AND NEW.idUser = Purchase.idUser AND NEW.idGame = Paid.idGame) 
  THEN
        RAISE EXCEPTION 'A user cant buy a game he already owns.';
    END IF;
  IF EXISTS 
    (SELECT * FROM Selling 
    WHERE NEW.idUser = Selling.idUser AND NEW.idGame = Selling.idGame) 
  THEN
        RAISE EXCEPTION 'A user cant buy a game being sold by him.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER BeforeInsertCart
    BEFORE Insert ON Cart
    FOR EACH ROW
    EXECUTE PROCEDURE BeforeInsertCart();

CREATE FUNCTION BeforeInsertVote() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NOT EXISTS 
    (SELECT * FROM Review 
    WHERE NEW.idUserReview = Review.idUser AND NEW.idGame = Review.idGame) 
  THEN
        RAISE EXCEPTION 'A user cant vote on a review that doesnt exist.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER BeforeInsertVote
    BEFORE Insert ON Vote
    FOR EACH ROW
    EXECUTE PROCEDURE BeforeInsertVote();

CREATE FUNCTION AfterInsertVote() RETURNS TRIGGER AS
$BODY$
BEGIN
  IF New.type 
  THEN
    UPDATE Review
    SET votes = votes + 1
    WHERE Review.idGame = NEW.idGame AND Review.idUser = NEW.idUserReview;
  ELSE 
    UPDATE Review
    SET votes = votes - 1
    WHERE Review.idGame = NEW.idGame AND Review.idUser = NEW.idUserReview;
  END IF;
  RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER AfterInsertVote
    AFTER INSERT ON Vote
    FOR EACH ROW
    EXECUTE PROCEDURE AfterInsertVote();

CREATE FUNCTION AfterDeleteVote() RETURNS TRIGGER AS
$BODY$
BEGIN
  IF OLD.type 
  THEN
    UPDATE Review
    SET votes = votes - 1
    WHERE Review.idGame = OLD.idGame AND Review.idUser = OLD.idUserReview;
  ELSE 
    UPDATE Review
    SET votes = votes + 1
    WHERE Review.idGame = OLD.idGame AND Review.idUser = OLD.idUserReview;
  END IF;
  RETURN NULL;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER AfterDeleteVote
    AFTER DELETE ON Vote
    FOR EACH ROW
    EXECUTE PROCEDURE AfterDeleteVote();

CREATE OR REPLACE FUNCTION InsertPaidGames(purchaseId integer, userId integer)
    RETURNS void AS
    $BODY$
        DECLARE
            paid_game integer;
        BEGIN
            FOR paid_game IN SELECT idGame FROM Cart WHERE idUser = userId
            LOOP
                INSERT INTO Paid (idPurchase, idGame, value) 
                SELECT purchaseId, paid_game, Game.price FROM Game WHERE id = paid_game;
            END LOOP;
        END;
    $BODY$
LANGUAGE 'plpgsql';



INSERT INTO Administrator(username, password) VALUES ('admin', '$2y$12$JPd1xrNkT5EZFgnpPOnDSudhRlYa2Jf1IVysG9LCBheg5cJZtAW5S');

INSERT INTO Country(name) VALUES ('Portugal');
INSERT INTO Country(name) VALUES ('Spain');
INSERT INTO Country(name) VALUES ('France');
INSERT INTO Country(name) VALUES ('United Kingdom');
INSERT INTO Country(name) VALUES ('United States of America');
INSERT INTO Country(name) VALUES ('Italy');
INSERT INTO Country(name) VALUES ('Germany');
INSERT INTO Country(name) VALUES ('Japan');
INSERT INTO Country(name) VALUES ('China');
INSERT INTO Country(name) VALUES ('South Korea');
INSERT INTO Country(name) VALUES ('Finland');
INSERT INTO Country(name) VALUES ('Romania');
INSERT INTO Country(name) VALUES ('Australia');
INSERT INTO Country(name) VALUES ('Russia');
INSERT INTO Country(name) VALUES ('Brazil');
INSERT INTO Country(name) VALUES ('Turkey');
INSERT INTO Country(name) VALUES ('Canada');
INSERT INTO Country(name) VALUES ('India');
INSERT INTO Country(name) VALUES ('Mexico');
INSERT INTO Country(name) VALUES ('Greece');


INSERT INTO Category (name) VALUES ('Action');
INSERT INTO Category (name) VALUES ('Adventure');
INSERT INTO Category (name) VALUES ('Fighter');
INSERT INTO Category (name) VALUES ('FPS');
INSERT INTO Category (name) VALUES ('MMO');
INSERT INTO Category (name) VALUES ('RPG');
INSERT INTO Category (name) VALUES ('Platform');
INSERT INTO Category (name) VALUES ('Simulation');
INSERT INTO Category (name) VALUES ('Sports');
INSERT INTO Category (name) VALUES ('Strategy');


INSERT INTO PaymentMethod(method) VALUES ('Paypal');
INSERT INTO PaymentMethod(method) VALUES ('Visa');
INSERT INTO PaymentMethod(method) VALUES ('MBWay');


INSERT INTO Image ("path") VALUES ('/img/users/image1.jpg');
INSERT INTO Image ("path") VALUES ('/img/users/image2.jpg');
INSERT INTO Image ("path") VALUES ('/img/users/image2.jpg');
INSERT INTO Image ("path") VALUES ('/img/users/image1.jpg');
INSERT INTO Image ("path") VALUES ('/img/users/image2.jpg');
INSERT INTO Image ("path") VALUES ('/img/users/image1.jpg');
INSERT INTO Image ("path") VALUES ('/img/users/image1.jpg');
INSERT INTO Image ("path") VALUES ('/img/game/massEffectAndromeda.jpg');
INSERT INTO Image ("path") VALUES ('/img/game/bioshock.jpg');
INSERT INTO Image ("path") VALUES ('/img/game/dracula.jpg');
INSERT INTO Image ("path") VALUES ('/img/game/neverwinter.jpg');
INSERT INTO Image ("path") VALUES ('/img/game/duck.jpg');
INSERT INTO Image ("path") VALUES ('/img/game/hexastar.png');
INSERT INTO Image ("path") VALUES ('/img/game/rocket.jpg');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');
INSERT INTO Image ("path") VALUES ('/img/game/default.png');


INSERT INTO "User"(username, email, password, profilePicture) VALUES ('johndoe','johndoe@gmail.com', '$2y$12$JPd1xrNkT5EZFgnpPOnDSudhRlYa2Jf1IVysG9LCBheg5cJZtAW5S', 1);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('janeroe','janeroe.55@hotmail.com', 'passroed', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('pitzer','pitzer.secondary@outlook.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('mary25','mary25@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('dragon2', 'dragon2o5@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('plican','plican.98@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('marthink','marthink@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('stringvarignon', 'stringvarignon@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('nappigeon', 'nappigeon@hotmail.com','pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('evie32', 'evie32@gmail.com','pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('studA', 'studA@hotmail.com','pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('bogieA', 'bogieA@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('stoker','stoker@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('aardwolf','aardwolf@gmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('sipeball','sipeball@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('minecraftLover','minecraftLover.55@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('game101','game101@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('screen07', 'screen07@gmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('pokeMaster', 'pokeMaster.55@hotmail.com', 'pass123', NULL);
INSERT INTO "User"(username, email, password, profilePicture) VALUES ('pikachu01','pikachu01.55@hotmail.com', 'pass123', NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('dLhDoPlWqWq9b','user1@email.com','DDwoTy3gWc8toYv',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('IwgW08nacdZ','user2@email.com','NAsbG How Z',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('3VnKLZD','user3@email.com','RufNRE5HdOJTyNlhUaM',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('6zxNuCnA6','user4@email.com','podSr2Bwz  6MbCewVU5',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('APVe2dC','user5@email.com','ofAPBfbC0 0a',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('LmpnnMkA','user6@email.com','fmjIJxjGPRrV7ebqHvo',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('FoH7PS','user7@email.com','GZGrY1XR0NOS',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('wRZrU987p4G','user8@email.com','lAg sM5430UOSbYKgkU',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('NNJvHDyrTdSRD','user9@email.com','lFqBcXeTOz Sjmx',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('VTsTR7gCsPAZd','usoljer10@email.com','aAsPcciRogNe6cPtcV',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('4LrDe','user11@email.com','VzEcexxcRk0g',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('6K2VvfG3DP','user12@email.com','wn IqHK0Ll',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('J6ksXi7SAoyi0ns','user13@email.com','gHgw1XUqL',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('ofS8fCu1maV','user14@email.com','Z6bXC93SZOznrL5MonY',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('upZUSTG','user15@email.com','hpRsyP8M53b5ikLODxoM',NULL);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('BuYNkWTKr7vDYvR','user16@email.com','rX8edp1S2WWAhpW6',5);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('jEjoQylzosp3','user17@email.com','SAuuurJ3CJHJPBG',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('qdyh52zYatkEuq','user18@email.com','OkIR0l3zUaMS',6);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('UBo7OJHXSll','user19@email.com','Gew mqKB 6jhij',4);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('yVG2qVH','user10@email.com','sqKfaNb1JXQUEloNs',1);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('YCnIZ5','user1@email.co',' H1CBY0CHoOyIKhcsSpq',5);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('xlN5SEOihhyA','user1@mail.com','rGq7Z9OwvA',5);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('n6mwj','user21@email.com','ThwBeY 71dJ8',1);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('BQr2v','user221@email.com','1tma98IK81RdhNoSpxWg',7);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('zwvwn502','user123@email.com','xT2sdj8k1ghs',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('m1LVVaLewI','user124@email.com','ZLUO6P9st9dNEAU',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('MP4UrMTkpQp5RgY','user132@email.com','pbzVZ Cl1',3);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('wnGLbh5uSvQkERN','user111@email.com','YTAUap7dO0mv39v6INGK',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('kvbiK','us1@email.com','8cKvuYsT Wdyje1',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('MhpqfWZHKag','useras1@email.com','CtpBeVHB',4);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('YFezJ','user1fads@email.com','bVkOOHygksMx9GHXk',3);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('UKeAskfyW','user1afas@email.com','rSkSTs82LS',6);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('w1C4rjjx8WE','user1gad@email.com','9EPsncPrzGEpZBjE',6);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('IQoHMJCsr4zxBK','userfa1@email.com','yDlTEKcJCyIeObNEidW',6);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('nW9vbyQVA1Ee','usergada1@email.com','4U6H7xHr84dPM',3);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('om0oQGv0kGOA','userad1@email.com','U7SUzu2OQKS3F',6);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('0a54BXw','usegadar1@email.com','ps6q WkV1ha',3);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('XXcQwtqteYK3Ud','usegadr1@email.com','Ncxa2R8H',1);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('s9vF6nSrsqKgz','usegaegr1@email.com','WH58KUWAYa1MTnP10',5);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('Apg4o5rfgXu','usehewdr1@email.com','sunqi MxoUF',5);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('R3uhMppveMIFdPJ','user1haf@email.com','fjg7ACsePvCVO7g',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('6ZrKMg7','useaghaher1@email.com','TJrUqAmbBUH7nZXj4ZZ',3);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('bI5YUOn5','uservvv1@email.com','D1e9pjM4EAY2p1ZPQ2f1',4);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('ZWIF9','useafafvr1@email.com','kbMUrUxp99EZ',3);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('UKzv3YDJoc','user1@ema.com','Vlkm6ax1',5);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('myQSAa2fU4G','usegageahdher1@email.com','I85b6xsieLdG6Rca1d',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('yEuPkah6Lka78C','user15432@email.com','GITaJMpfP5Nq',3);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('dspAYl7gMkCYax','use54r1@email.com','b E5Nv1tG5X',2);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('hI2lJ4KoYR','usea22sar1@email.com','3XKAguE1 SSZBzy',4);
INSERT INTO "User" (username, email, password,profilePicture) VALUES ('Tjxbjkbp50AR','use2a5r1@email.com','sGHTJUW9',2);

INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (1 , 'John Doe', 1, 'Male', '1990-04-27');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (2 , 'Jane Roe', 2, 'Female', '1998-08-13');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (3 , 'Nigel Noel', 2, 'Other', '1988-06-04');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (4 , 'Mary Marks', 7, 'Female', '1992-08-13');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (5 , 'Darnell Morrison', 3, 'Other', '1972-10-20');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (6 , 'Violet Aguirre', 12, 'Female', '1992-11-03');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (7 , 'Mark Stinson', 8, 'Male', '1992-03-02');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (8 , 'Hanna Nicholson', 2, 'Female', '1995-08-31');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (9 , 'Farah Cook', 20, 'Female', '1985-03-22');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (10 , 'Darrel Paul', 8, 'Male', '1999-02-23');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (11 , 'Keith Herring', 2, 'Male', '1989-05-07');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (12 , 'Jaylen Hogg', 10, 'Other', '1979-03-22');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (13 , 'Juliet Hunt', 2, 'Female', '1998-01-28');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (14 , 'Codie Mustafa', 19, 'Other', '1994-10-27');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (15 , 'Colm Floyd', 2, 'Other', '1994-08-03');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (16 , 'Sebastien King', 2, 'Male', '1988-12-03');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (17 , 'Jan Conroy', 9, 'Male', '1978-02-01');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (18 , 'Tye Mcfarland', 9, 'Male', '1997-08-16');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (19 , 'Vinnie Cain', 1, 'Female', '1993-10-13');
INSERT INTO NormalUser(id, name, idCountry, gender, birthdate) VALUES (20 , 'Finn Phelps', 20, 'Male', '1999-02-03');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (21,'Vincent Pollard',1,'Female','1994-01-22');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (22,'Luke Arnold',3,'Female','1974-10-13');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (23,'Lana Daniels',7,'Other','1999-12-27');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (24,'Emi Bowman',17,'Female','1991-11-19');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (25,'Ruby Roy',14,'Female','1971-02-07');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (26,'Jillian Nolan',1,'Other','1994-02-18');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (27,'Fredericka Hood',8,'Female','1970-04-30');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (28,'Aidan Stanley',9,'Other','1982-06-03');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (29,'Amos Obrien',7,'Other','1991-04-20');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (30,'Whilemina Ewing',19,'Female','1988-06-04');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (31,'William Doyle',4,'Male','1996-02-27');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (32,'Mufutau Mullen',3,'Other','1984-12-07');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (33,'Oleg James',17,'Other','1983-08-30');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (34,'Xandra Gillespie',10,'Male','1990-02-06');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (35,'Isadora Wright',6,'Female','1979-07-10');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (36,'Kane Russell',10,'Male','1992-04-15');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (37,'Jessica Bolton',14,'Other','1995-08-12');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (38,'Mariko Baldwin',10,'Other','1998-10-10');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (39,'Chancellor House',7,'Other','1998-05-01');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (40,'Rachel Morton',3,'Other','1987-03-30');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (41,'Todd Matthews',17,'Female','1975-12-23');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (42,'Howard Livingston',3,'Male','1977-07-12');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (43,'Destiny Calderon',14,'Female','1993-02-06');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (44,'Porter Leach',8,'Other','1990-04-16');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (45,'Kyle Terrell',3,'Male','1995-06-20');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (46,'Emma Nieves',4,'Other','1980-12-05');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (47,'Stewart Luna',14,'Male','1975-04-20');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (48,'Sasha Cochran',1,'Other','1971-01-26');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (49,'Castor Dixon',4,'Other','1997-05-29');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (50,'Micah Schwartz',15,'Male','1991-07-04');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (51,'Timothy Sullivan',16,'Female','1995-12-27');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (52,'Jared Hooper',11,'Other','1997-09-05');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (53,'Chase Strickland',8,'Male','1970-10-09');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (54,'Maya Foster',4,'Male','1996-09-01');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (55,'Martin Figueroa',19,'Other','1998-08-22');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (56,'Oliver Solis',20,'Male','1997-03-13');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (57,'Rashad Gallegos',17,'Male','1988-02-09');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (58,'Christen Huff',4,'Other','1980-05-11');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (59,'Derek Key',8,'Male','1983-07-17');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (60,'Jerry Ochoa',17,'Female','1982-08-24');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (61,'Raphael Clarke',20,'Other','1983-11-05');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (62,'Ciaran Livingston',17,'Female','1985-09-28');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (63,'Bell Glenn',7,'Male','1983-01-09');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (64,'Anne Small',11,'Male','1989-12-16');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (65,'Chester Yang',14,'Other','1993-04-05');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (66,'Colleen Buckner',3,'Female','1985-08-19');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (67,'Melodie Whitaker',11,'Female','1996-07-30');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (68,'Coby Jacobs',13,'Female','1978-10-21');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (69,'Tad Neal',12,'Female','1996-12-25');
INSERT INTO NormalUser (id, name,idCountry,gender,birthdate) VALUES (70,'Justin Finch',17,'Male','1994-11-02');


INSERT INTO BannedUser(id, reason) VALUES (3, 'Credit card fraud');
INSERT INTO BannedUser(id, reason) VALUES (17, 'Game piracy');
INSERT INTO BannedUser(id, reason) VALUES (8, 'Spam on reviews');
INSERT INTO BannedUser(id, reason) VALUES (5, 'Harassment on reviews');
INSERT INTO BannedUser(id, reason) VALUES (70, 'Harassment on reviews');
INSERT INTO BannedUser(id, reason) VALUES (66, 'Spam on reviews');
INSERT INTO BannedUser(id, reason) VALUES (54, 'Spam on reviews');
INSERT INTO BannedUser(id, reason) VALUES (44, 'Spam on reviews');


INSERT INTO Seller(id) VALUES (1);
INSERT INTO Seller(id) VALUES (6);
INSERT INTO Seller(id) VALUES (7);
INSERT INTO Seller(id) VALUES (12);
INSERT INTO Seller(id) VALUES (13);
INSERT INTO Seller(id) VALUES (14);
INSERT INTO Seller(id) VALUES (15);
INSERT INTO Seller(id) VALUES (16);
INSERT INTO Seller(id) VALUES (17);
INSERT INTO Seller(id) VALUES (18);
INSERT INTO Seller(id) VALUES (19);
INSERT INTO Seller(id) VALUES (20);


INSERT INTO Developer(pen_name, company, url) VALUES ('Bioware', ' Electronic Arts', 'http://www.bioware.com/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Psyonix', 'Psyonix', 'https://psyonix.com/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Landon Podbielski', 'Adult Swim Games', 'http://www.superjoebob.com/');
INSERT INTO Developer(pen_name) VALUES ('Stoker Hunt');
INSERT INTO Developer(pen_name, company, url) VALUES ('Eric Barone', 'Chucklefish', 'https://www.stardewvalley.net/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Bethesda', 'Bethesda', 'https://bethesda.net/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Crystal Dynamics', 'Square Enix', 'https://crystald.com/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Irrational Games', '2K Games', 'https://www.ghoststorygames.com/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Nicalis', 'Nicalis', 'https://www.nicalis.com/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Stunlock Studios', 'Stunlock Studios ', 'https://www.stunlock.com/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Moppin', 'Devolver Digital', 'http://www.downwellgame.com/');
INSERT INTO Developer(pen_name, company, url) VALUES ('Double Fine Productions', 'Majesco Entertainment', 'http://doublefine.com/');


INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover) 
VALUES ('Mass Effect Andromeda', 'Mass Effect: Andromeda takes you to the Andromeda galaxy, far beyond the Milky Way. There, youll lead our fight for a new home in hostile territory - where WE are the aliens. Play as the Pathfinder - a leader of a squad of military-trained explorers - with deep progression and customisation systems. This is the story of humanity’s next chapter, and your choices throughout the game will ultimately determine our survival in the Andromeda Galaxy. As you unfold the mysteries of the Andromeda Galaxy and the hope for humanity lies on your shoulders – You must ask yourself… How far will you go?', 
1, 39.95, 'Mass Effect: Andromeda is an action role-playing video game developed by BioWare and published by Electronic Arts for Microsoft Windows, PlayStation 4, and Xbox One. Released worldwide in March 2017, it is the fourth major entry in the Mass Effect series and the first since Mass Effect 3', 0, '/games/dummy.txt', 16, 'Accepted', '2017-03-21 04:35:12 -8:00', NULL, 8);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover) 
VALUES ('Rocket League', 'Winner or Nominee of more than 150 "Best of 2015" Awards, including Game of the Year, Best Sports Game, and Best Multiplayer Game, Rocket League® combines soccer with driving in an unbelievable physics-based multiplayer-focused sequel to Supersonic Acrobatic Rocket-Powered Battle-Cars! Choose from a variety of high-flying vehicles equipped with huge rocket boosters to score amazing aerial goals and pull off incredible, game-changing saves!', 
2, 14.99, 'Rocket League is a vehicular soccer video game developed and published by Psyonix. The game was first released for Microsoft Windows and PlayStation 4 in July 2015, with ports for Xbox One, macOS, Linux, and Nintendo Switch being released later on.', 4.2, '/games/dummy.txt', 3, 'Accepted', '2015-07-07 14:05:14 -8:00', NULL, 14);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Duck Game', 'Enter the futuristic year of 1984, an age where ducks run wild in a frantic battle for glory. Win over the crowd and gain a following by blasting your feathered friends with Shotguns, Net Guns, Mind Control Rays, Saxophones, Magnet Guns, and pretty much anything else a duck could use as a weapon. One hit and youre roasted. This is DUCK GAME. Dont blink.', 
3, 9.99, 'Duck Game is an action game developed by Landon Podbielski and published by Adult Swim Games.', 0, '/games/dummy.txt', 7, 'Accepted', '2014-05-13 22:17:52 -8:00', NULL, 12);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Hexastar', 'Hexastar is a strategy game where the objective is to turn the hexagons into stars by moving a certain number of sticks.', 
4, 7.99, 'Hexastar was developed by a freelancer associated with Apex Games.', 0, '/games/dummy.txt', 3, 'Accepted', '2014-05-14 22:17:52 -8:00', NULL, 13);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Red Stoker', 'Red Stocker is a game where the objective is to find the vampire who has been killing people during the night and defeat it.', 
4, 19.99, 'Red Stocker is inspired by Bram Stokers top seller Dracula.', 0, '/games/dummy.txt', 18, 'Accepted', '2014-05-14 22:23:52 -8:00', NULL, 10);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Anthem', 'Anthem combines third-person shooter and action role-playing game elements in a "contiguous open world" shared with up to three other players. Each player takes the role of a Freelancer donning fully customizable exosuits called Javelins. Players can build relationships with various non-playable characters, but they cannot establish romantic relationships with them.', 
1, 29.95, 'Anthem is an online multiplayer action role-playing video game developed by BioWare and published by Electronic Arts.', 0, '/games/dummy.txt', 16, 'Accepted', '2019-02-13 22:17:52 -8:00', NULL, 20);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Neverwinter Nights', 'The story begins with the player character, under the guidance of Lady Aribeth, being sent to recover four creatures (dryad, intellect devourer, yuan-ti, and cockatrice), known collectively as the Waterdhavian creatures. ', 
1, 19.95, 'Neverwinter Nights is a third-person role-playing video game developed by BioWare.', 0, '/games/dummy.txt', 16, 'Accepted', '2002-10-13 20:02:34 -8:00', NULL, 11);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Stardew Valley', 'Lugina e Stardew është një RPG pa vend në jetë! Ju keni trashëguar komplotin e vjetër të gjyshit tuaj në Stardew Valley. Armatosur me vegla me dorë dhe me disa monedha, ju filluat të filloni jetën tuaj të re. A mund të mësoni të jetoni jashtë tokës dhe ti ktheni këto fusha të mëdha në një shtëpi të lulëzuar? Nuk do të jetë e lehtë. Që kur Joja Korporata erdhi në qytet, mënyrat e vjetra të jetës kanë zhdukur të gjitha. Qendra e bashkësisë, dikur qendra më e gjallë e aktivitetit të qytetit, tani qëndron në rrëmujë. Por lugina duket plot mundësi. Me një përkushtim të vogël, ju mund të jeni vetëm ai që do ta riktheni Stardew Valley në madhështi!',
5, 13.99, 'Stardew Valley është një video-lojë simuluese bujqësore e zhvilluar nga Eric ConcernedApe Barone dhe botuar fillimisht nga Chucklefish.', 0, '/games/dummy.txt', 0, 'Rejected', '2016-2-26 5:31:56 -8:00', 'Description must be in english', 15);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('TESV: Skyrim', 'The Elder Scrolls V: Skyrim, the 2011 Game of the Year, is the next chapter in the highly anticipated Elder Scrolls saga. Developed by Bethesda Game Studios, the 2011 Studio of the Year, that brought you Oblivion and Fallout 3. Skyrim reimagines and revolutionizes the open-world fantasy epic, bringing to life a complete virtual world open for you to explore any way you choose.',
6, 15.99, 'The Elder Scrolls V: Skyrim is an action role-playing video game developed by Bethesda Game Studios and published by Bethesda Softworks.', 0, '/games/dummy.txt', 18, 'Accepted', '2011-11-11 8:23:15 -8:00', NULL, 16);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Tomb Raider', 'Tomb Raider explores the intense and gritty origin story of Lara Croft and her ascent from a young woman to a hardened survivor. Armed only with raw instincts and the ability to push beyond the limits of human endurance, Lara must fight to unravel the dark history of a forgotten island to escape its relentless hold. Download the Turning Point trailer to see the beginning of Lara’s epic adventure.',
7, 19.99, 'Tomb Raider is an action-adventure video game developed by Crystal Dynamics and published by Square Enix.', 0, '/games/dummy.txt', 18, 'Rejected', '2013-3-5 10:00:30 -8:00', NULL, 20);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Bioshock Infinite', 'Indebted to the wrong people, with his life on the line, veteran of the U.S. Cavalry and now hired gun, Booker DeWitt has only one opportunity to wipe his slate clean. He must rescue Elizabeth, a mysterious girl imprisoned since childhood and locked up in the flying city of Columbia.',
8, 29.99, 'BioShock Infinite is a first-person shooter video game developed by Irrational Games and published by 2K Games.', 0, '/games/dummy.txt', 18, 'Accepted', '2013-3-25 11:13:52 -8:00', NULL, 9);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('The Binding of Isaac: Rebirth', 'The Binding of Isaac: Rebirth is a randomly generated action RPG shooter with heavy Rogue-like elements. Following Isaac on his journey players will find bizarre treasures that change Isaacs form giving him super human abilities and enabling him to fight off droves of mysterious creatures and discover secrets.',
9, 14.99, 'The Binding of Isaac: Rebirth is an indie roguelike video game designed by Edmund McMillen and developed and published by Nicalis.', 0, '/games/dummy.txt', 16, 'Accepted', '2014-11-4 23:15:44 -8:00', NULL, 19);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Battlerite', 'Battlerite​ ​is​ ​a​ ​PvP​ ​arena​ ​brawler​ ​and​ ​the​ ​spiritual​ ​successor​ ​to​ ​the critically​ ​acclaimed​ ​Bloodline Champions.​ ​Experience​ ​the​ ​unique​ ​combination​ of a ​​top​-down​ ​shooter​ ​meeting a ​fast​-paced fighting​ ​game and take ​part​ ​in​ ​highly​ ​competitive,​ ​adrenaline-fueled​ ​2v2​ ​and​ ​3v3​ ​battles.​',
10, 10.99, 'Battlerite is a free-to-play team-based action game based on multiplayer online battle arena (MOBA) gameplay developed and published by Stunlock Studios.', 0, '/games/dummy.txt', 12, 'Accepted', '2015-4-24 21:06:22 -8:00', NULL, 20);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Downwell', 'Downwell is a curious game about a young person venturing down a well in search of untold treasures with only his Gunboots for protection. Make your way further and further down into the darkness filled with nasty creatures and mysterious secrets to collect the spectacular red gems scattered about the rocks.​',
11, 2.99, 'Downwell is a roguelike vertically scrolling shooter platform video game developed by Ojiro Moppin Fumoto and published by Devolver Digital.', 0, '/games/dummy.txt', 0, 'Accepted', '2015-10-15 15:55:56 -8:00', NULL, 21);
INSERT INTO Game(name, description, idDeveloper, price, briefDescription, score, "path", ageRestriction, state, releaseDate, rejectionReason, cover)
VALUES ('Psychonauts', 'This classic action/adventure platformer from acclaimed developers Double Fine Productions follows the story of a young psychic named Razputin. In his quest to join the Psychonauts--an elite group of international psychic secret agents--he breaks into their secret training facility: Whispering Rock Psychic Summer Camp.',
12, 9.99, 'Psychonauts is a platform game developed by Double Fine Productions that first released in 2005.', 0, '/games/dummy.txt', 3, 'Accepted', '2005-4-19 8:30:00 -8:00', NULL, 22);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('est','Curabitur consequat dictum accumsan sit velit Aenean ad magnis Ut id ridiculus metus eros tempus enim Sed Nam facilisi Integer nisi Morbi tellus Ut est pede',4,52.59,'aptent Donec velit mus',0,'hETlewSfaOB',0,'Accepted','2013-12-09 15:15:34',NULL,19);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('Ut lacinia gravida','imperdiet nulla dis Nunc dolor amet',7,48.45,'Duis nec',0,'OlHmpIufPRejdVX',18,'Pending','2009-06-18 20:30:20',NULL,11);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('augue elementum','lorem Vivamus massa nibh orci viverra imperdiet tincidunt mollis nibh fames iaculis interdum primis venenatis Proin Praesent Integer sodales lacus magnis posuere Morbi sit fermentum diam massa dui pharetra Nam Aliquam ultricies Mauris nisl mauris enim suscipit tortor imperdiet ullamcorper',3,14.04,'non montes netus tincidunt Quisque fermentum egestas tristique Etiam Integer',0,'BQuidQzDRRFYfGapDSrVXJiq',7,'Rejected','2016-05-18 07:46:36',NULL,13);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('feugiat varius sociosqu Integer','tempor convallis justo ante ornare auctor erat augue porttitor Class parturient fermentum et lectus',2,9.99,'dapibus sociosqu mi magnis adipiscing arcu',0,'YlATlrsBODbHIGhwHct',0,'Pending','2018-11-05 05:48:06',NULL,12);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('faucibus vel','faucibus ipsum netus eget a ac Lorem rhoncus',11,15.73,'ornare consectetuer Curae',0,'tnMsUiPHXBqnktlEqJqvarE',7,'Rejected','2009-02-06 06:16:20',NULL,22);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('aliquet ut','Lorem arcu Aliquam vestibulum pulvinar tristique Pellentesque aliquet Integer dapibus aptent elementum ornare',6,42.87,'pulvinar turpis',0,'RgOIdIrGcwnPbY',18,'Accepted','2017-04-07 13:32:44',NULL,17);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('nisl natoque','lobortis aliquam Proin morbi Suspendisse netus odio justo lectus nascetur Pellentesque habitant dolor habitant Nunc nascetur commodo pretium nonummy tempor porttitor Nulla accumsan nonummy sem nisi Nunc primis fames est Cum erat risus Sed risus',9,33.36,'nisl diam',0,'uSYnFmXhqHJFEHylmgWE',0,'Accepted','2011-06-21 10:12:16',NULL,20);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('Aenean sapien bibendum','vehicula sollicitudin risus ridiculus Vestibulum condimentum eget blandit nibh velit ultricies eros',6,13.36,'dapibus magnis Quisque Integer faucibus morbi in eleifend',0,'GDWrkPgfXQS',3,'Rejected','2013-03-22 01:08:55',NULL,11);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('Nulla magna','eget nibh sed hendrerit mus porttitor sociis nunc leo primis inceptos Fusce accumsan quis libero a Nunc sociosqu magna sollicitudin parturient imperdiet felis purus Sed sociis Pellentesque',12,24.97,'nisi primis eros netus morbi tristique id tellus varius',0,'vqJQTswmPJNVGYAxOiglWtU',12,'Rejected','2018-10-14 08:31:06',NULL,20);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('nunc molestie tortor Pellentesque','Maecenas Proin urna scelerisque quam eros eleifend magna hendrerit egestas at magna porttitor molestie eu cursus tempus auctor leo Nam rhoncus magna Mauris lacinia torquent vehicula torquent consequat molestie rhoncus Pellentesque natoque Duis torquent sodales lacinia erat conubia',4,44.49,'et lorem venenatis vulputate interdum faucibus vitae Curae consectetuer torquent',0,'EFqsHSeptEnyoKR',7,'Rejected','2016-05-25 13:41:35',NULL,13);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('Fusce fames pharetra mus','dictum Morbi mi sem senectus libero hymenaeos Curae congue aliquam non mollis sollicitudin cubilia euismod Duis Fusce interdum',4,52.88,'mi aptent lorem dis scelerisque',0,'KwEBbnsgNqFtWPmZ',3,'Pending','2015-03-01 21:28:44',NULL,20);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('vel id dis risus','eros quis dis pharetra Curabitur ligula Maecenas ornare neque',8,32.52,'sodales sed justo porta facilisis',0,'bZliHetFdGiKoGY',3,'Rejected','2017-02-02 17:02:40',NULL,19);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('malesuada primis','dis fringilla tincidunt blandit amet facilisis sed magnis sagittis Etiam vestibulum Donec nunc auctor nibh sociosqu commodo est magna In elit blandit nibh tellus faucibus sapien purus vestibulum morbi Etiam elit conubia Nullam sagittis sem dapibus aptent lacus convallis a',12,56.83,'justo Nullam massa felis urna a tortor rhoncus accumsan pede',0,'uhVQjPmAVWOuiPsOHO',18,'Rejected','2007-11-02 23:14:47',NULL,20);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('semper','cursus litora ornare natoque odio convallis bibendum mus vel dictum sociis arcu cursus mi tempus hendrerit eros facilisi bibendum a est accumsan volutpat lacinia Vestibulum',11,31.75,'leo ultricies',0,'iipfyhqsuFMHRKE',7,'Pending','2007-08-04 19:35:32',NULL,10);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('ac morbi malesuada tristique','tempus velit facilisi fermentum Vestibulum fermentum per Nullam mollis urna fames viverra vel dictum luctus mattis ante nisi',3,3.97,'dolor ligula scelerisque eros',0,'cmlcPUrlyFAQtNtBTDaF',7,'Pending','2005-05-04 08:45:06',NULL,22);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('tristique elit inceptos','ullamcorper vitae pretium et cursus aliquet Suspendisse gravida ipsum torquent egestas laoreet in lectus Cum id feugiat eget Pellentesque eros convallis ut est',11,58.64,'pulvinar interdum fringilla varius litora ullamcorper Cras urna rutrum',0,'GaVGWQSmckaabe',12,'Pending','2013-01-19 11:37:02',NULL,14);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('eu ut','metus sit pellentesque senectus tempus sit Pellentesque ornare laoreet nascetur fringilla egestas tristique semper',7,51.15,'Fusce Nunc molestie fermentum feugiat consequat',0,'ayJzbjlPFLGyTXq',0,'Rejected','2018-04-15 20:55:01',NULL,22);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('ornare interdum nostra','bibendum per morbi cursus Proin fames vestibulum',12,14.53,'eros venenatis adipiscing nisl lorem lobortis',0,'fUVhccPGPKrQr',7,'Pending','2008-08-17 17:48:22',NULL,11);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('ligula','egestas mollis vulputate eleifend fames dis metus iaculis a Quisque condimentum sem eget sagittis Quisque taciti venenatis nisl posuere litora auctor in aliquam congue turpis sagittis venenatis leo quis nostra Lorem iaculis lobortis nisi amet Ut Curabitur volutpat',10,40.40,'facilisi Praesent Aliquam magnis Quisque vitae velit non Curae',0,'qEwOXEVNbZbXivkTtZo',18,'Accepted','2008-02-21 05:32:41',NULL,14);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('vel ligula diam erat','sem eget parturient tortor conubia nascetur justo dictum elit metus commodo magnis vitae Lorem orci accumsan adipiscing tortor Etiam senectus lectus mi dui Proin nostra mollis leo Nulla Class malesuada tristique Morbi magna ut',1,46.57,'aliquet ornare aliquet gravida Cras turpis',0,'zIylGVqrRJSFvu',12,'Rejected','2012-02-14 08:18:47',NULL,21);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('ullamcorper Proin','Proin pulvinar fames egestas rhoncus nisi ipsum bibendum Quisque posuere Aenean congue taciti auctor Cum libero elementum sagittis pellentesque turpis id amet fringilla blandit Duis dis luctus fringilla nibh turpis ut penatibus',10,6.62,'arcu parturient purus',0,'UriunZhsYiuo',18,'Rejected','2018-01-26 13:16:36',NULL,21);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('at','suscipit Mauris neque velit vulputate Curabitur mollis sociosqu at Etiam posuere nunc accumsan Nam fermentum condimentum ornare facilisi penatibus Nulla Donec aliquam taciti dis pede dapibus nonummy amet nec ridiculus netus amet eget turpis placerat Etiam eget Proin',6,48.70,'auctor penatibus Aenean nulla Aenean eleifend condimentum magna urna sollicitudin',0,'pRivRihlbgruUbmlfszOBK',7,'Pending','2013-05-29 12:35:03',NULL,8);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('ultrices faucibus','Proin quam litora Integer nibh Ut nonummy pretium neque magnis Maecenas convallis volutpat aptent netus lacinia torquent',9,14.23,'Etiam ultrices sagittis',0,'QoBWYwffcyfNWUAwiSrFG',16,'Rejected','2007-06-10 13:39:32',NULL,17);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('Aenean nisi fames Ut','nibh Proin gravida lobortis sociis id arcu non non ante penatibus vulputate Aliquam suscipit odio Duis taciti bibendum Nunc porttitor hendrerit senectus Curae adipiscing Phasellus condimentum pretium consequat eu tortor congue ullamcorper Aenean ornare volutpat sagittis eu nec',4,26.55,'vestibulum Vestibulum est sociis magna',0,'WYzjzORVcXJVzd',16,'Accepted','2018-01-05 16:52:19',NULL,10);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('metus netus','Cum aliquam nisi ad aliquam posuere aliquet Class consectetuer posuere neque nunc egestas consequat interdum nunc Curabitur nunc Duis mollis',2,34.38,'aliquam facilisi massa elit tortor',0,'ULmCKkTgfYlsutnr',12,'Accepted','2011-06-29 08:40:03',NULL,20);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('id placerat ante','libero rutrum euismod conubia nibh Integer est lorem tortor viverra vulputate rutrum sociis Fusce adipiscing mus rutrum ultricies Mauris tortor venenatis sociosqu congue risus',9,30.83,'justo felis sodales ut viverra tortor ligula purus',0,'YmNzRSMdohircWPUhBlc',16,'Accepted','2007-04-28 04:15:15',NULL,9);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('taciti','cubilia auctor leo Etiam Vestibulum pretium ut tempor erat lacus Integer dis vulputate ridiculus Duis elit Cras Nunc magna torquent facilisis condimentum ad malesuada sit erat placerat tempus ut',10,56.10,'Morbi mattis',0,'DNwrpSNTceBtnNHAHaKQxEn',12,'Accepted','2015-07-21 08:42:35',NULL,18);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('ad','auctor suscipit tempor elit eget nostra velit pede nostra nunc penatibus congue porta Duis malesuada sociosqu consectetuer Vestibulum Sed fermentum Aenean varius dignissim neque imperdiet a faucibus dapibus congue erat per vestibulum bibendum purus iaculis',7,46.30,'ornare ac dictum a Class Duis tincidunt malesuada vestibulum',0,'CchJHRylEFUSSLzgh',0,'Rejected','2016-01-09 21:06:02',NULL,20);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('torquent consectetuer id','tempus metus elementum Morbi litora ut consectetuer conubia tincidunt Quisque neque condimentum Sed',9,38.91,'eu tincidunt',0,'boCjBOQTut',16,'Accepted','2006-02-06 04:20:53',NULL,13);
INSERT INTO Game (name,description,idDeveloper,price,briefDescription,score,"path",ageRestriction,state,releaseDate,rejectionReason,cover) VALUES ('fermentum morbi metus','elit Maecenas parturient congue ad ante interdum in Integer',5,44.49,'suscipit Nunc ligula tempor Quisque ad dapibus Praesent sagittis netus',0,'WZRuukkgeFnjjb',0,'Accepted','2018-05-22 12:48:15',NULL,19);


INSERT INTO GameCategory (idGame,idCategory) VALUES (20,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (42,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (6,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (28,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (41,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (24,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (15,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (32,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (2,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (16,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (20,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (40,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (44,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (20,1);
INSERT INTO GameCategory (idGame,idCategory) VALUES (21,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (36,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (23,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (3,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (38,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (40,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (31,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (12,8);
INSERT INTO GameCategory (idGame,idCategory) VALUES (24,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (15,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (35,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (29,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (18,8);
INSERT INTO GameCategory (idGame,idCategory) VALUES (32,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (15,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (35,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (42,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (15,8);
INSERT INTO GameCategory (idGame,idCategory) VALUES (41,8);
INSERT INTO GameCategory (idGame,idCategory) VALUES (33,9);
INSERT INTO GameCategory (idGame,idCategory) VALUES (45,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (38,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (16,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (27,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (30,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (42,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (26,9);
INSERT INTO GameCategory (idGame,idCategory) VALUES (18,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (23,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (26,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (17,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (40,9);
INSERT INTO GameCategory (idGame,idCategory) VALUES (21,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (44,1);
INSERT INTO GameCategory (idGame,idCategory) VALUES (41,9);
INSERT INTO GameCategory (idGame,idCategory) VALUES (26,1);
INSERT INTO GameCategory (idGame,idCategory) VALUES (42,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (27,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (35,8);
INSERT INTO GameCategory (idGame,idCategory) VALUES (2,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (16,9);
INSERT INTO GameCategory (idGame,idCategory) VALUES (7,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (39,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (34,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (5,1);
INSERT INTO GameCategory (idGame,idCategory) VALUES (22,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (10,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (34,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (24,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (4,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (1,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (29,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (43,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (11,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (16,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (3,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (5,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (6,10);
INSERT INTO GameCategory (idGame,idCategory) VALUES (16,8);
INSERT INTO GameCategory (idGame,idCategory) VALUES (44,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (22,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (6,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (23,5);
INSERT INTO GameCategory (idGame,idCategory) VALUES (25,4);
INSERT INTO GameCategory (idGame,idCategory) VALUES (12,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (39,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (40,6);
INSERT INTO GameCategory (idGame,idCategory) VALUES (2,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (40,2);
INSERT INTO GameCategory (idGame,idCategory) VALUES (25,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (8,7);
INSERT INTO GameCategory (idGame,idCategory) VALUES (30,8);
INSERT INTO GameCategory (idGame,idCategory) VALUES (8,3);
INSERT INTO GameCategory (idGame,idCategory) VALUES (12,9);
INSERT INTO GameCategory (idGame,idCategory) VALUES (27,8);

INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (1, 1, '2017-03-20 09:05:06 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (6, 2, '2015-07-05 19:28:55 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (7, 3, '2014-05-11 18:02:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (13, 4, '2014-05-13 04:05:06 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (13, 5, '2014-05-13 04:09:33 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (4, 6, '2019-01-29 17:25:17 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (4, 7, '2002-10-12 23:32:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (12, 8, '2016-2-20 9:00:00 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (14, 9, '2011-11-2 13:07:23 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (15, 10, '2013-2-27 16:23:47 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (16, 11, '2013-3-18 20:55:34 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (17, 12, '2014-10-20 15:55:00 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (18, 13, '2015-4-14 10:36:20 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (19, 14, '2015-10-5 11:35:32 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (20, 15, '2005-4-10 15:11:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (14, 16, '2013-12-20 09:05:06 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (14, 17, '2010-01-05 19:28:55 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (17, 18, '2017-05-11 18:02:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (13, 19, '2019-05-13 04:05:06 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (13, 20, '2010-05-13 04:09:33 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (1, 21, '2017-01-29 17:25:17 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (1, 22, '2012-10-12 23:32:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (19, 23, '2019-2-20 9:00:00 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (1, 24, '2019-11-2 13:07:23 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (15, 25, '2019-2-27 16:23:47 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (16, 26, '2019-3-18 20:55:34 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (17, 27, '2019-10-20 15:55:00 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (18, 28, '2019-4-14 10:36:20 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (19, 29, '2015-10-5 11:35:32 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (20, 30, '2019-4-10 15:11:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (19, 31, '2019-12-20 09:05:06 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (20, 32, '2019-01-05 19:28:55 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (17, 33, '2019-05-11 18:02:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (12, 34, '2019-05-13 04:05:06 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (13, 35, '2019-05-13 04:09:33 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (6, 36, '2019-01-29 17:25:17 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (6, 37, '2019-10-12 23:32:37 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (6, 38, '2019-2-20 9:00:00 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (7, 39, '2019-11-2 13:07:23 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (15, 40, '2019-2-27 16:23:47 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (16, 41, '2019-3-18 20:55:34 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (7, 42, '2019-10-20 15:55:00 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (18, 43, '2019-4-14 10:36:20 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (19, 44, '2019-10-5 11:35:32 -8:00');
INSERT INTO Selling(idUser, idGame, releaseDate) VALUES (20, 45, '2019-4-10 15:11:37 -8:00');


INSERT INTO ComplementaryImage (idImage, idGame) VALUES (35, 1);
INSERT INTO ComplementaryImage (idImage, idGame) VALUES (34, 3);
INSERT INTO ComplementaryImage (idImage, idGame) VALUES (33, 15);
INSERT INTO ComplementaryImage (idImage, idGame) VALUES (32, 15);
INSERT INTO ComplementaryImage (idImage, idGame) VALUES (31, 15);
INSERT INTO ComplementaryImage (idImage, idGame) VALUES (29, 30);
INSERT INTO ComplementaryImage (idImage, idGame) VALUES (30, 43);

INSERT INTO Review(idGame, idUser, content, score) VALUES (1, 1, 'Its a great game, lots of fun!', 3.5);
INSERT INTO Review(idGame, idUser, content, score) VALUES (3, 1, 'I love its retro style and simplicity! I feel like I could play for hours...', 4.2);
INSERT INTO Review(idGame, idUser, content, score) VALUES (4, 4, 'Fun game I enjoy playing it, not like other games I have played. I have played for awhile now, and I honestly love it, cannot put it down completed 400 leaves after I downloaded this fun, amazing, addicting game. This would be the best download you could ask for. Thank you for this fun, enjoyable game', 4.7);
INSERT INTO Review(idGame, idUser, content, score) VALUES (7, 7, 'Neverwinter Nights is one of those exceedingly rare games that has a lot to offer virtually everyone, even if they arent already into RPGs.', 4.6);
INSERT INTO Review(idGame, idUser, content, score) VALUES (5, 5, 'Not the best game I have ever played but its allright. Defenitely worth the buy.', 3.7);
INSERT INTO Review (idUser,idGame,content,score) VALUES (15,5,'Aliquam malesuada sem Class per metus Class orci urna tellus semper dictum fermentum porta eleifend viverra sapien ligula urna posuere ut iaculis Nam',3.0);
INSERT INTO Review (idUser,idGame,content,score) VALUES (13,10,'Duis gravida bibendum dignissim sociis mauris netus tincidunt fermentum Integer amet leo interdum a',1.1);
INSERT INTO Review (idUser,idGame,content,score) VALUES (16,8,'Suspendisse condimentum fermentum rutrum fames fermentum mus ad felis facilisis dictum Praesent purus aliquam risus ridiculus porta scelerisque parturient hymenaeos semper sem Lorem dignissim Integer malesuada senectus pharetra vel et nunc dolor egestas sit viverra nisi dapibus rhoncus adipiscing ut',2.5);
INSERT INTO Review (idUser,idGame,content,score) VALUES (14,11,'Lorem Quisque Phasellus quam sapien erat bibendum eros nisl diam lectus pellentesque litora erat velit Donec auctor Nulla eu facilisi rutrum ipsum eros Lorem Etiam Nunc placerat gravida ultrices in morbi risus tortor Mauris diam ornare Curabitur dignissim sodales',4.9);
INSERT INTO Review (idUser,idGame,content,score) VALUES (19,1,'In feugiat nisi ac velit Nullam lacus Curae odio ipsum sem arcu augue porttitor placerat euismod semper Morbi rhoncus nibh auctor iaculis Curae mauris dapibus mus aptent',2.3);
INSERT INTO Review (idUser,idGame,content,score) VALUES (12,10,'lobortis magna sapien pretium mi fringilla torquent molestie magna nascetur lacinia Lorem iaculis primis Curabitur parturient per Morbi ipsum rhoncus nec lorem arcu dui viverra Integer natoque Praesent quam mattis erat lectus congue facilisis consequat torquent vitae quis varius sodales Curabitur ridiculus imperdiet Quisque convallis taciti vel ut dui',2.0);
INSERT INTO Review (idUser,idGame,content,score) VALUES (20,13,'viverra neque Donec consectetuer dolor Class at vulputate Nam parturient volutpat feugiat Integer ipsum tincidunt netus posuere pellentesque leo ad et ante ornare fames mus laoreet posuere Proin Vivamus eu lacinia',1.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (14,9,'ligula dapibus mollis Phasellus feugiat condimentum Praesent elementum',3.6);
INSERT INTO Review (idUser,idGame,content,score) VALUES (6,14,'porta ad mi aliquam Nunc erat a commodo Cras et Pellentesque Cras orci tristique nostra elementum ridiculus Quisque cursus',1.7);
INSERT INTO Review (idUser,idGame,content,score) VALUES (4,12,'scelerisque per fames hymenaeos Nullam sed habitant condimentum mollis Cum Maecenas imperdiet Quisque hendrerit cursus enim nunc elit varius justo ipsum pellentesque dui vitae libero',2.5);
INSERT INTO Review (idUser,idGame,content,score) VALUES (15,12,'posuere varius Cum risus sem fames elementum Nullam dapibus nisl felis Nunc sagittis Class quis tincidunt adipiscing tristique lacinia felis Class pellentesque at mattis facilisi varius euismod leo felis arcu',2.8);
INSERT INTO Review (idUser,idGame,content,score) VALUES (19,15,'leo mollis ac pharetra metus Nam sed libero rutrum erat',0.7);
INSERT INTO Review (idUser,idGame,content,score) VALUES (17,4,'lobortis fermentum cursus sapien vel interdum Vestibulum pretium gravida morbi at tempus pretium Curae',1.6);
INSERT INTO Review (idUser,idGame,content,score) VALUES (6,13,'Curabitur nec Nunc nisi pellentesque venenatis consequat congue auctor consectetuer Morbi egestas scelerisque leo eros habitant porta felis eros ut ultrices mattis varius nisi nisl neque ac tristique risus scelerisque egestas Aenean suscipit dictum sem ultrices fringilla placerat cursus Fusce Ut interdum id id bibendum Nam viverra',2.0);
INSERT INTO Review (idUser,idGame,content,score) VALUES (15,3,'sapien id justo amet tempor orci facilisi purus senectus commodo est Lorem gravida fringilla pretium fermentum convallis torquent sapien nostra egestas interdum a velit lacinia volutpat felis nascetur conubia lorem pulvinar egestas pretium lectus Mauris nostra mollis ligula tincidunt pulvinar Lorem sapien',0.1);
INSERT INTO Review (idUser,idGame,content,score) VALUES (16,4,'sagittis penatibus vitae leo Vivamus parturient blandit commodo molestie sem montes natoque nisi a In ridiculus malesuada Integer ornare fringilla facilisis Cum eleifend cubilia pulvinar faucibus nulla congue lobortis tellus',4.8);
INSERT INTO Review (idUser,idGame,content,score) VALUES (8,4,'fermentum tristique ipsum nunc volutpat felis condimentum Aliquam Nullam',4.5);
INSERT INTO Review (idUser,idGame,content,score) VALUES (2,11,'per pulvinar nisi taciti ullamcorper justo velit blandit montes montes porttitor Morbi Class porta Curabitur iaculis varius pellentesque rutrum malesuada primis Etiam',1.5);
INSERT INTO Review (idUser,idGame,content,score) VALUES (13,15,'porttitor mattis tortor nibh Maecenas bibendum Nam parturient ornare In nulla vestibulum id vel Aliquam risus aliquam magnis fames facilisis aliquet Nullam facilisi Pellentesque varius commodo neque velit volutpat amet non nunc Sed ornare Aliquam cursus euismod est vitae auctor fermentum',0.5);
INSERT INTO Review (idUser,idGame,content,score) VALUES (8,9,'id netus Ut hymenaeos eleifend mattis bibendum venenatis odio condimentum sit semper augue',1.8);
INSERT INTO Review (idUser,idGame,content,score) VALUES (3,5,'massa netus faucibus purus tempor nostra Nulla lobortis elit porta arcu lectus aliquet felis Morbi sapien facilisis lacinia tristique natoque pede',0.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (8,11,'justo Cum velit Suspendisse ipsum Quisque parturient tristique eu mollis vitae dignissim at Nunc ultricies habitant ligula tristique Quisque vulputate odio Curabitur fames sem laoreet Sed Aliquam ligula pretium vulputate Aliquam Integer at velit tempor dis ornare aptent pellentesque fringilla hendrerit Proin nibh quis sapien non habitant nibh',1.8);
INSERT INTO Review (idUser,idGame,content,score) VALUES (18,7,'amet justo tristique torquent cubilia Ut odio leo aliquam venenatis elit Nunc In augue dapibus Lorem dapibus inceptos mus magna litora torquent dapibus libero porttitor erat Aenean leo pharetra interdum primis convallis Cum Cras magnis tortor pellentesque Nulla consequat fames neque ac tristique mauris adipiscing',1.7);
INSERT INTO Review (idUser,idGame,content,score) VALUES (17,12,'orci Cum massa iaculis ultricies fringilla Lorem hendrerit lectus taciti tempus Class eget lectus egestas Pellentesque pulvinar commodo tincidunt Aliquam magnis vulputate et',2.9);
INSERT INTO Review (idUser,idGame,content,score) VALUES (18,12,'Integer orci fames ultrices dignissim sociis aptent aliquet hymenaeos placerat sed Nunc habitant ut nulla eleifend mi euismod nec Cras',5.0);
INSERT INTO Review (idUser,idGame,content,score) VALUES (5,14,'tincidunt sem dapibus pulvinar nunc tincidunt Nam eget congue lacinia massa aptent nonummy sapien suscipit imperdiet auctor dis ad Aliquam condimentum Duis metus suscipit arcu feugiat vehicula imperdiet litora porta felis congue magna Phasellus molestie congue adipiscing',1.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (19,6,'congue enim ad faucibus Donec est facilisi inceptos lacus faucibus facilisis adipiscing aliquet porta libero leo convallis nibh elit Proin neque quam gravida Fusce convallis orci Cras senectus habitant vel risus dis eu sollicitudin tellus turpis',2.0);
INSERT INTO Review (idUser,idGame,content,score) VALUES (16,3,'aptent ridiculus scelerisque ante Class libero rutrum primis',3.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (9,15,'consequat suscipit eleifend sem rutrum montes In vel mauris Nulla lacus nisl scelerisque sapien pulvinar accumsan Etiam commodo ad lacus sociis In sociosqu In sociosqu fringilla enim Lorem dis odio natoque cursus erat accumsan Nulla quis',0.4);
INSERT INTO Review (idUser,idGame,content,score) VALUES (10,11,'nunc tellus ipsum luctus scelerisque auctor velit libero Fusce fermentum viverra leo laoreet vitae cubilia rutrum Praesent volutpat mi euismod vulputate condimentum congue faucibus sagittis nulla conubia interdum sociis parturient ad urna sed habitant adipiscing vel vel nascetur fermentum consequat euismod facilisis',0.4);
INSERT INTO Review (idUser,idGame,content,score) VALUES (18,8,'pellentesque a sit ligula feugiat pede diam elementum Ut pharetra sem lacinia',1);
INSERT INTO Review (idUser,idGame,content,score) VALUES (11,13,'pellentesque ante Cras torquent arcu sociis montes Etiam facilisi consectetuer sociosqu semper mollis felis turpis dolor ultrices posuere convallis mattis dis risus dapibus In Duis venenatis Praesent',1.9);
INSERT INTO Review (idUser,idGame,content,score) VALUES (3,12,'lacus facilisis euismod Nunc id id dictum parturient ante nibh elit consectetuer natoque nec fringilla sit ante blandit amet',0.1);
INSERT INTO Review (idUser,idGame,content,score) VALUES (8,2,'Cras vel Donec in risus venenatis conubia Maecenas dignissim sollicitudin ultricies lorem tempus primis sed purus vehicula odio tempus ut interdum inceptos magna aptent suscipit gravida rutrum Aenean vestibulum felis felis ornare',5.0);
INSERT INTO Review (idUser,idGame,content,score) VALUES (18,14,'dis sit a porttitor lobortis penatibus Vivamus interdum inceptos penatibus Ut in aliquet ut accumsan Morbi enim',4.9);
INSERT INTO Review (idUser,idGame,content,score) VALUES (15,1,'In Vivamus lacinia ultricies Praesent nunc Maecenas primis lorem Vestibulum sociis sapien',4.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (20,5,'sit congue dignissim nostra nonummy libero commodo dolor Nunc Pellentesque mattis quis tempus sollicitudin adipiscing netus sagittis fringilla blandit sociosqu pede Pellentesque diam parturient mus ullamcorper vestibulum lobortis dignissim imperdiet commodo massa Vivamus lobortis sagittis dis Fusce aptent Praesent ut Mauris facilisis cursus odio odio ridiculus',3.4);
INSERT INTO Review (idUser,idGame,content,score) VALUES (7,15,'interdum Cras urna fringilla lacinia at est porttitor amet enim felis cursus posuere gravida',4.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (15,6,'sociis hymenaeos lacinia fames habitant fames fringilla Etiam dapibus vulputate tempus euismod nulla lacus aptent molestie nulla nascetur In pede morbi ac rutrum tristique sagittis mauris ac odio pellentesque laoreet Proin viverra vulputate vestibulum condimentum Aliquam Vivamus dis risus sit eros auctor ac Nulla',2.1);
INSERT INTO Review (idUser,idGame,content,score) VALUES (11,15,'adipiscing auctor nostra sollicitudin Duis imperdiet Nunc primis Cum accumsan rutrum iaculis orci imperdiet facilisi lacinia felis adipiscing dictum urna mollis aliquam mus sapien varius lobortis dapibus leo nascetur dictum In fringilla posuere Class lorem',1.0);
INSERT INTO Review (idUser,idGame,content,score) VALUES (3,7,'turpis mus semper lacinia Mauris In',4.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (2,8,'at condimentum Lorem eros Proin Class eros Vivamus erat vestibulum mauris velit Phasellus metus vehicula eu parturient id id per Morbi morbi viverra nonummy torquent Vestibulum vehicula eleifend Fusce leo nibh commodo volutpat convallis vitae hendrerit turpis vitae purus accumsan ornare pellentesque Aenean gravida lacinia nisl Morbi',4.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (18,1,'Aliquam viverra ac Proin erat Pellentesque adipiscing odio Suspendisse Maecenas mi Donec congue Nulla magna Lorem Pellentesque tortor egestas interdum imperdiet porta consectetuer mollis senectus id rhoncus dignissim Cum lectus Curabitur libero ipsum placerat quam Phasellus Nulla Nulla malesuada sagittis ligula Etiam a',1.7);
INSERT INTO Review (idUser,idGame,content,score) VALUES (4,14,'laoreet fermentum massa ridiculus Class sociis urna dapibus Curae condimentum',3.5);
INSERT INTO Review (idUser,idGame,content,score) VALUES (5,3,'eros morbi massa tempus rutrum facilisi In congue sociosqu aptent suscipit nascetur varius nibh',3.8);
INSERT INTO Review (idUser,idGame,content,score) VALUES (7,11,'senectus varius faucibus Morbi urna molestie quis placerat adipiscing Lorem feugiat volutpat tristique lacus odio Ut mus orci porttitor consectetuer facilisi montes enim parturient tincidunt Sed Cum facilisi parturient nisl a netus per Cras massa ligula Phasellus nonummy magna tortor Cum luctus ultricies Curabitur',4.2);
INSERT INTO Review (idUser,idGame,content,score) VALUES (1,11,'varius dictum Vivamus natoque Nulla tempor posuere ornare cursus ultrices facilisi inceptos mi volutpat mus magnis turpis placerat augue Curae congue auctor accumsan et cursus',4.7);
INSERT INTO Review (idUser,idGame,content,score) VALUES (16,1,'Hate the game!',1);
INSERT INTO Review (idUser,idGame,content,score) VALUES (21,1,'Magnifique! Trully wonderful game!',5);
INSERT INTO Review (idUser,idGame,content,score) VALUES (32,1,'Meh...',3);


INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 15, 1, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 1, 2, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 1, 3, FALSE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 1, 4, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 1, 12, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 1, 17, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 1, 10, FALSE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (1, 1, 7, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (3, 1, 9, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (3, 1, 3, FALSE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (3, 1, 5, FALSE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (3, 1, 12, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (3, 1, 13, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (3, 1, 10, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (3, 1, 17, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 32, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 30, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 29, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 27, FALSE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 10, FALSE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 40, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 42, TRUE);
INSERT INTO Vote(idGame, idUserReview, idUserVote, type) VALUES (11, 1, 31, TRUE);

INSERT INTO Favorite(idUser, idGame) VALUES (1, 13);
INSERT INTO Favorite(idUser, idGame) VALUES (1, 11);
INSERT INTO Favorite(idUser, idGame) VALUES (1, 9);
INSERT INTO Favorite(idUser, idGame) VALUES (1, 6);
INSERT INTO Favorite(idUser, idGame) VALUES (1, 3);
INSERT INTO Favorite(idUser, idGame) VALUES (2, 5);
INSERT INTO Favorite(idUser, idGame) VALUES (2, 7);
INSERT INTO Favorite(idUser, idGame) VALUES (3, 10);
INSERT INTO Favorite(idUser, idGame) VALUES (4, 3);
INSERT INTO Favorite(idUser, idGame) VALUES (5, 12);
INSERT INTO Favorite(idUser, idGame) VALUES (5, 11);
INSERT INTO Favorite(idUser, idGame) VALUES (7, 8);
INSERT INTO Favorite(idUser, idGame) VALUES (7, 9);
INSERT INTO Favorite(idUser, idGame) VALUES (8, 1);
INSERT INTO Favorite(idUser, idGame) VALUES (9, 1);
INSERT INTO Favorite(idUser, idGame) VALUES (9, 12);
INSERT INTO Favorite(idUser, idGame) VALUES (10, 13);
INSERT INTO Favorite(idUser, idGame) VALUES (14, 1);
INSERT INTO Favorite(idUser, idGame) VALUES (14, 2);
INSERT INTO Favorite(idUser, idGame) VALUES (16, 3);
INSERT INTO Favorite(idUser, idGame) VALUES (20, 15);
INSERT INTO Favorite(idUser, idGame) VALUES (40, 42);
INSERT INTO Favorite(idUser, idGame) VALUES (14, 33);
INSERT INTO Favorite(idUser, idGame) VALUES (16, 32);
INSERT INTO Favorite(idUser, idGame) VALUES (36, 12);
INSERT INTO Favorite(idUser, idGame) VALUES (36, 10);
INSERT INTO Favorite(idUser, idGame) VALUES (36, 7);
INSERT INTO Favorite(idUser, idGame) VALUES (36, 2);
INSERT INTO Favorite(idUser, idGame) VALUES (36, 41);
INSERT INTO Favorite(idUser, idGame) VALUES (36, 29);
INSERT INTO Favorite(idUser, idGame) VALUES (36, 19);
INSERT INTO Favorite(idUser, idGame) VALUES (40, 10);
INSERT INTO Favorite(idUser, idGame) VALUES (43, 7);
INSERT INTO Favorite(idUser, idGame) VALUES (44, 2);
INSERT INTO Favorite(idUser, idGame) VALUES (50, 41);
INSERT INTO Favorite(idUser, idGame) VALUES (51, 29);
INSERT INTO Favorite(idUser, idGame) VALUES (53, 7);
INSERT INTO Favorite(idUser, idGame) VALUES (24, 2);
INSERT INTO Favorite(idUser, idGame) VALUES (37, 41);
INSERT INTO Favorite(idUser, idGame) VALUES (12, 29);

INSERT INTO Cart(idUser, idGame) VALUES (1, 3);
INSERT INTO Cart(idUser, idGame) VALUES (1, 7);
INSERT INTO Cart(idUser, idGame) VALUES (1, 9);
INSERT INTO Cart(idUser, idGame) VALUES (5, 4);
INSERT INTO Cart(idUser, idGame) VALUES (9, 2);
INSERT INTO Cart(idUser, idGame) VALUES (9, 12);
INSERT INTO Cart(idUser, idGame) VALUES (7, 4);
INSERT INTO Cart(idUser, idGame) VALUES (10, 5);
INSERT INTO Cart(idUser, idGame) VALUES (2, 4);
INSERT INTO Cart(idUser, idGame) VALUES (2, 1);
INSERT INTO Cart(idUser, idGame) VALUES (4, 2);
INSERT INTO Cart(idUser, idGame) VALUES (4, 3);
INSERT INTO Cart(idUser, idGame) VALUES (44, 2);
INSERT INTO Cart(idUser, idGame) VALUES (44, 1);
INSERT INTO Cart(idUser, idGame) VALUES (44, 22);
INSERT INTO Cart(idUser, idGame) VALUES (44, 35);
INSERT INTO Cart(idUser, idGame) VALUES (44, 7);
INSERT INTO Cart(idUser, idGame) VALUES (44, 10);
INSERT INTO Cart(idUser, idGame) VALUES (50, 41);
INSERT INTO Cart(idUser, idGame) VALUES (51, 29);

INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (1, DEFAULT, 0, 222555666, 1);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (5, DEFAULT, 0, 657896545, 2);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (9, DEFAULT, 0, 234443422, 1);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (7, DEFAULT, 0, 123142453, 2);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (10, DEFAULT, 0, 753453453, 1);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (2, DEFAULT, 0, 324234233, 3);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (1, DEFAULT, 0, 222555666, 1);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (44, DEFAULT, 0, 327345233, 2);
INSERT INTO Purchase(idUser, purchaseDate, totalPaid, nif, idPaymentMethod) VALUES (44, DEFAULT, 0, 327345233, 2);

INSERT INTO Paid(idGame, idPurchase, value) VALUES (3, 1, 9.99);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (4, 2, 7.99);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (2, 3, 14.99);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (4, 4, 7.99);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (5, 5, 19.99);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (4, 6, 14.99);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (1, 6, 39.95);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (7, 7, 39.95);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (7, 8, 12.35);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (10, 8, 19.95);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (22, 9, 12.35);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (35, 9, 19.95);
INSERT INTO Paid(idGame, idPurchase, value) VALUES (1, 9, 17.75);