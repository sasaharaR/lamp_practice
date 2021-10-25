CREATE TABLE `purchase_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `create_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY('history_id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `purchase_detail` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` varchar(100) NOT NULL,
  'att_price' int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `create_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY('history_id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;