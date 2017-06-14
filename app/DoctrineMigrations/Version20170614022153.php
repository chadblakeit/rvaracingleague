<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170614022153 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `drivers` (`id`, `firstname`, `lastname`, `number`, `sponsor`, `team`, `carmake`, `inactive`) VALUES
                        (1, 'Jimmie', 'Johnson', 48, 'Lowe''s', 'Hendrick Motorsports', 'chevrolet', 0),
                        (2, 'Joey', 'Logano', 22, 'Shell Pennzoil', 'Team Penske', 'ford', 0),
                        (3, 'Kyle', 'Busch', 18, 'M&M''s Core', 'Joe Gibbs Racing', 'toyota', 0),
                        (4, 'Matt', 'Kenseth', 20, 'DeWalt Flexvolt', 'Joe Gibbs Racing', 'toyota', 0),
                        (5, 'Denny', 'Hamlin', 11, 'FedEx Express', 'Joe Gibbs Racing', 'toyota', 0),
                        (6, 'Kurt', 'Busch', 41, 'Monster Energy', 'Stewart-Haas Racing', 'ford', 0),
                        (7, 'Kevin', 'Harvick', 4, 'Jimmy John''s', 'Stewart-Haas Racing', 'ford', 0),
                        (8, 'Kyle', 'Larson', 42, 'Target', 'Chip Ganassi Racing', 'chevrolet', 0),
                        (9, 'Chase', 'Elliott', 24, 'NAPA Auto Parts', 'Hendrick Motorsports', 'chevrolet', 0),
                        (10, 'Martin', 'Truex Jr', 78, 'Bass Pro Shops / TRACKER Boats', 'Furniture Row Racing', 'toyota', 0),
                        (11, 'Brad', 'Keselowski', 2, 'Miller Lite', 'Team Penske', 'ford', 0),
                        (12, 'Jamie', 'McMurray', 1, 'McDonald''s', 'Chip Ganassi Racing', 'chevrolet', 0),
                        (13, 'Austin', 'Dillon', 3, 'Dow Corning', 'Richard Childress Racing', 'chevrolet', 0),
                        (14, 'Chris', 'Buescher', 34, 'TBA', 'JTG Daugherty Racing', 'chevrolet', 0),
                        (15, 'Kasey', 'Kahne', 5, 'Great Clips', 'Hendrick Motorsports', 'chevrolet', 0),
                        (16, 'Ryan', 'Newman', 31, 'Caterpillar', 'Richard Childress Racing', 'chevrolet', 0),
                        (17, 'AJ', 'Allmendinger', 47, 'TBD', 'JTG Daugherty Racing', 'chevrolet', 0),
                        (18, 'Ryan', 'Blaney', 21, 'Motorcraft / Quick Lane Tire & Auto Center', 'Wood Brothers Racing', 'ford', 0),
                        (19, 'Ricky', 'Stenhouse Jr', 17, 'Fastenal', 'Roush Fenway Racing', 'ford', 0),
                        (20, 'Trevor', 'Bayne', 6, 'Advocare', 'Roush Fenway Racing', 'ford', 0),
                        (21, 'Greg', 'Biffle', 16, '', '', '', 1),
                        (22, 'Danica', 'Patrick', 10, 'Aspen Dental', 'Stewart-Haas Racing', 'ford', 0),
                        (23, 'Paul', 'Menard', 27, 'Peak/Menards', 'Richard Childress Racing', 'chevrolet', 0),
                        (24, 'Aric', 'Almirola', 43, 'Smithfield Foods', 'Richard Petty Motorsports', 'ford', 1),
                        (25, 'Clint', 'Bowyer', 14, 'Mobil 1', 'Stewart-Haas Racing', 'ford', 0),
                        (26, 'Casey', 'Mears', 13, '', '', '', 1),
                        (27, 'Landon', 'Cassill', 38, 'Love''s Travel Stops', 'Front Row Motorsports', 'ford', 0),
                        (28, 'Michael', 'McDowell', 95, 'Klove Radio / WRL Contractors', 'Leavine Family Racing', 'chevrolet', 0),
                        (29, 'Brian', 'Scott', 44, '', '', '', 1),
                        (30, 'Dale', 'Earnhardt Jr', 88, 'Nationwide', 'Hendrick Motorsports', 'chevrolet', 0),
                        (31, 'David', 'Ragan', 23, 'TBD', 'Front Row Motorsports', 'ford', 0),
                        (32, 'Regan', 'Smith', 7, '', '', '', 0),
                        (33, 'Matt', 'DiBenedetto', 49, 'EJ Wade Foundation', 'Go Fast Racing', 'ford', 0),
                        (34, 'Michael', 'Annett', 46, '', '', '', 1),
                        (35, 'Cole', 'Whitt', 98, 'Florida Lottery', 'Tristar Motorsports', 'ford', 0),
                        (36, 'Jeff', 'Gordon', 88, '', '', '', 1),
                        (37, 'Reed', 'Sorenson', 55, 'TBD', 'Premium Motorsports', 'chevrolet', 1),
                        (38, 'Josh', 'Wise', 30, '', '', '', 1),
                        (39, 'Jeffrey', 'Earnhardt', 83, 'Little Joes Autos / Curtis Key Plumbing', 'Circle Sport / TMG', 'chevrolet', 0),
                        (40, 'Brian', 'Vickers', 14, '', '', '', 1),
                        (41, 'Bobby', 'Labonte', 32, '', '', '', 1),
                        (42, 'David', 'Gilliland', 35, '', '', '', 1),
                        (43, 'Michael', 'Waltrip', 15, 'Aaron''s', 'Premium Motorsports', 'toyota', 1),
                        (44, 'Boris', 'Said', 32, '', '', '', 1),
                        (45, 'Patrick', 'Carpentier', 32, '', '', '', 1),
                        (46, 'Gray', 'Gaulding', 30, '', '', '', 0),
                        (47, 'Eddie', 'MacDonald', 32, '', '', '', 1),
                        (48, 'Alex', 'Kennedy', 55, '', '', '', 1),
                        (49, 'Ty', 'Dillon', 13, 'Geico', 'Germain Racing', 'chevrolet', 0),
                        (50, 'Joey', 'Gase', 23, 'Best Home Furnishings', 'BK Racing', 'toyota', 1),
                        (51, 'Brendan', 'Gaughan', 75, 'BBeard Oil Distributing / TTS Logistics', 'Beard Motorsports', 'chevrolet', 1),
                        (52, 'Erik', 'Jones', 77, '5-Hour Energy Extra Strength', 'Furniture Row Racing', 'toyota', 0),
                        (53, 'D.J.', 'Kennington', 96, 'Lordico / Castrol', 'Gaunt Brothers Racing', 'toyota', 1),
                        (54, 'Corey', 'Lajoie', 83, 'TBD', 'BK Racing', 'toyota', 0),
                        (55, 'Daniel', 'Suarez', 19, 'Arris', 'Joe Gibbs Racing', 'toyota', 0),
                        (56, 'Elliott', 'Sadler', 7, 'Golden Corral', 'Tommy Baldwin Racing', 'chevrolet', 1),
                        (57, 'Cody', 'Ware', 51, 'NA', 'NA', 'chevrolet', 0),
                        (58, 'Derrick', 'Cope', 55, 'NA', 'NA', 'chevrolet', 0),
                        (59, 'Timmy', 'Hill', 39, 'n/a', 'n/a', 'chevrolet', 1),
                        (60, 'JJ', 'Yeley', 27, 'n/a', 'n/a', 'chevrolet', 1),
                        (61, 'Carl', 'Long', 66, 'n/a', 'n/a', 'chevrolet', 1),
                        (62, 'Ryan', 'Sieg', 83, 'n/a', 'n/a', 'to', 1),
                        (63, 'Ross', 'Chastain', 15, 'n/a', 'n/a', 'chevrolet', 1),
                        (64, 'Darrell', 'Wallace Jr', 43, 'n/a', 'n/a', 'ford', 0);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
