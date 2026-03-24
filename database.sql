DROP TABLE IF EXISTS canzoni;

CREATE TABLE IF NOT EXISTS canzoni (
    id INT(11) NOT NULL AUTO_INCREMENT,
    titolo VARCHAR(255) NOT NULL,
    artista VARCHAR(255) DEFAULT NULL,
    link VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY link (link)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO canzoni (titolo, artista, link) VALUES
('Stairway to Heaven', 'Led Zeppelin', 'canzoni/Led%20Zeppelin%20-%20Stairway%20To%20Heaven%20(Official%20Audio).mp3'),
('Seven Nation Army', 'The White Stripes', 'canzoni/The%20White%20Stripes%20-%20Seven%20Nation%20Army%20(Official%20Music%20Video).mp3'),
('We Are The Champions', 'Queen', 'canzoni/Queen%20-%20We%20Are%20The%20Champions%20(Live).mp3'),
('Thunderstruck', 'AC/DC', 'canzoni/ACDC%20-%20Thunderstruck%20(Official%20Video).mp3'),
('Another Brick In The Wall', 'Pink Floyd', 'canzoni/pink%20floyd%20-%20another%20brick%20in%20the%20wall.mp3');