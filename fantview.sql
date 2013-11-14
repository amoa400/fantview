-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 14, 2013 at 08:13 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fantview`
--

-- --------------------------------------------------------

--
-- Table structure for table `ftv_answer`
--

CREATE TABLE IF NOT EXISTS `ftv_answer` (
  `candidate_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` varchar(20000) NOT NULL,
  `score` smallint(6) NOT NULL,
  PRIMARY KEY (`candidate_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ftv_answer`
--

INSERT INTO `ftv_answer` (`candidate_id`, `question_id`, `answer`, `score`) VALUES
(1, 88, '/*\nPROG:milk3\nID:asiapea1\nLANG:C\n*/\n#include <stdio.h>\n#include <stdlib.h>\n#include <string.h>\n#include <assert.h>\n#include <ctype.h>\n\n#define MAX 20\n\ntypedef struct State	State;\nstruct State {\n    int a[3];\n};\n\nint seen[MAX+1][MAX+1][MAX+1];\nint canget[MAX+1];\n\nState\nstate(int a, int b, int c)\n{\n    State s;\n\n    s.a[0] = a;\n    s.a[1] = b;\n    s.a[2] = c;\n    return s;\n}\n\nint cap[3];\n\n/* pour from bucket "from" to bucket "to" */\nState\npour(State s, int from, int to)\n{\n    int amt;\n\n    amt = s.a[from];\n    if(s.a[to]+amt > cap[to])\n	amt = cap[to] - s.a[to];\n\n    s.a[from] -= amt;\n    s.a[to] += amt;\n    return s;\n}\n\nvoid\nsearch(State s)\n{\nwhile(1){}\n    int i, j;\n\n    if(seen[s.a[0]][s.a[1]][s.a[2]])\n	return;\n\n    seen[s.a[0]][s.a[1]][s.a[2]] = 1;\n\n    if(s.a[0] == 0)	/* bucket A empty */\n	canget[s.a[2]] = 1;\n\n    for(i=0; i<3; i++)\n    for(j=0; j<3; j++)\n	search(pour(s, i, j));	\n}\n\nint main(void)\n{\n    int i;\n    FILE *fin, *fout;\n    char *sep;\n    \n    fin = stdin;\n    fout = stdout;\n\n    //fin = fopen("milk3.in", "r");\n   //fout = fopen("milk3.out", "w");\n    assert(fin != NULL && fout != NULL);\n\n    fscanf(fin, "%d %d %d", &cap[0], &cap[1], &cap[2]);\n\n    search(state(0, 0, cap[2]));\n\n    sep = "";\n    for(i=0; i<=cap[2]; i++) {\n	if(canget[i]) {\n	    fprintf(fout, "%s%d", sep, i);\n	    sep = " ";\n	}\n    }\n    fprintf(fout, "\\n");\n\n    return 0;\n}\n', 0),
(1, 881, '/*\r\nPROG:milk3\r\nID:asiapea1\r\nLANG:C\r\n*/\r\n#include <stdio.h>\r\n#include <stdlib.h>\r\n#include <string.h>\r\n#include <assert.h>\r\n#include <ctype.h>\r\n\r\n#define MAX 20\r\n\r\ntypedef struct State	State;\r\nstruct State {\r\n    int a[3];\r\n};\r\n\r\nint seen[MAX+1][MAX+1][MAX+1];\r\nint canget[MAX+1];\r\n\r\nState\r\nstate(int a, int b, int c)\r\n{\r\n    State s;\r\n\r\n    s.a[0] = a;\r\n    s.a[1] = b;\r\n    s.a[2] = c;\r\n    return s;\r\n}\r\n\r\nint cap[3];\r\n\r\n/* pour from bucket "from" to bucket "to" */\r\nState\r\npour(State s, int from, int to)\r\n{\r\n    int amt;\r\n\r\n    amt = s.a[from];\r\n    if(s.a[to]+amt > cap[to])\r\n	amt = cap[to] - s.a[to];\r\n\r\n    s.a[from] -= amt;\r\n    s.a[to] += amt;\r\n    return s;\r\n}\r\n\r\nvoid\r\nsearch(State s)\r\n{\r\n    int i, j;\r\n\r\n    if(seen[s.a[0]][s.a[1]][s.a[2]])\r\n	return;\r\n\r\n    seen[s.a[0]][s.a[1]][s.a[2]] = 1;\r\n\r\n    if(s.a[0] == 0)	/* bucket A empty */\r\n	canget[s.a[2]] = 1;\r\n\r\n    for(i=0; i<3; i++)\r\n    for(j=0; j<3; j++)\r\n	search(pour(s, i, j));	\r\n}\r\n\r\nint main(void)\r\n{\r\n    int i;\r\n    FILE *fin, *fout;\r\n    char *sep;\r\n    \r\n    fin = stdin;\r\n    fout = stdout;\r\n\r\n    //fin = fopen("milk3.in", "r");\r\n   //fout = fopen("milk3.out", "w");\r\n    assert(fin != NULL && fout != NULL);\r\n\r\n    fscanf(fin, "%d %d %d", &cap[0], &cap[1], &cap[2]);\r\n\r\n    search(state(0, 0, cap[2]));\r\n\r\n    sep = "";\r\n    for(i=0; i<=cap[2]; i++) {\r\n	if(canget[i]) {\r\n	    fprintf(fout, "%s%d", sep, i);\r\n	    sep = " ";\r\n	}\r\n    }\r\n    fprintf(fout, "\\n");\r\n\r\n    return 0;\r\n}\r\n', 0),
(1, 884, '/*\r\nPROG:milk3\r\nID:asiapea1\r\nLANG:C\r\n*/\r\n#include <stdio.h>\r\n#include <stdlib.h>\r\n#include <string.h>\r\n#include <assert.h>\r\n#include <ctype.h>\r\n\r\n#define MAX 20\r\n\r\ntypedef struct State	State;\r\nstruct State {\r\n    int a[3];\r\n};\r\n\r\nint seen[MAX+1][MAX+1][MAX+1];\r\nint canget[MAX+1];\r\n\r\nState\r\nstate(int a, int b, int c)\r\n{\r\n    State s;\r\n\r\n    s.a[0] = a;\r\n    s.a[1] = b;\r\n    s.a[2] = c;\r\n    return s;\r\n}\r\n\r\nint cap[3];\r\n\r\n/* pour from bucket "from" to bucket "to" */\r\nState\r\npour(State s, int from, int to)\r\n{\r\n    int amt;\r\n\r\n    amt = s.a[from];\r\n    if(s.a[to]+amt > cap[to])\r\n	amt = cap[to] - s.a[to];\r\n\r\n    s.a[from] -= amt;\r\n    s.a[to] += amt;\r\n    return s;\r\n}\r\n\r\nvoid\r\nsearch(State s)\r\n{\r\n    int i, j;\r\n\r\n    if(seen[s.a[0]][s.a[1]][s.a[2]])\r\n	return;\r\n\r\n    seen[s.a[0]][s.a[1]][s.a[2]] = 1;\r\n\r\n    if(s.a[0] == 0)	/* bucket A empty */\r\n	canget[s.a[2]] = 1;\r\n\r\n    for(i=0; i<3; i++)\r\n    for(j=0; j<3; j++)\r\n	search(pour(s, i, j));	\r\n}\r\n\r\nint main(void)\r\n{\r\n    int i;\r\n    FILE *fin, *fout;\r\n    char *sep;\r\n    \r\n    fin = stdin;\r\n    fout = stdout;\r\n\r\n    //fin = fopen("milk3.in", "r");\r\n   //fout = fopen("milk3.out", "w");\r\n    assert(fin != NULL && fout != NULL);\r\n\r\n    fscanf(fin, "%d %d %d", &cap[0], &cap[1], &cap[2]);\r\n\r\n    search(state(0, 0, cap[2]));\r\n\r\n    sep = "";\r\n    for(i=0; i<=cap[2]; i++) {\r\n	if(canget[i]) {\r\n	    fprintf(fout, "%s%d", sep, i);\r\n	    sep = " ";\r\n	}\r\n    }\r\n    fprintf(fout, "\\n");\r\n\r\n    return 0;\r\n}\r\n', 0),
(1, 886, '/*\nPROG:milk3\nID:asiapea1\nLANG:C\n*/\n#include <stdio.h>\n#include <stdlib.h>\n#include <string.h>\n#include <assert.h>\n#include <ctype.h>\n\n#define MAX 20\n\ntypedef struct State	State;\nstruct State {\n    int a[3];\n};\n\nint seen[MAX+1][MAX+1][MAX+1];\nint canget[MAX+1];\n\nState\nstate(int a, int b, int c)\n{\n    State s;\n\n    s.a[0] = a;\n    s.a[1] = b;\n    s.a[2] = c;\n    return s;\n}\n\nint cap[3];\n\n/* pour from bucket "from" to bucket "to" */\nState\npour(State s, int from, int to)\n{\n    int amt;\n\n    amt = s.a[from];\n    if(s.a[to]+amt > cap[to])\n	amt = cap[to] - s.a[to];\n\n    s.a[from] -= amt;\n    s.a[to] += amt;\n    return s;\n}\n\nvoid\nsearch(State s)\n{\n    int i, j;\n\n    if(seen[s.a[0]][s.a[1]][s.a[2]])\n	return;\n\n    seen[s.a[0]][s.a[1]][s.a[2]] = 1;\n\n    if(s.a[0] == 0)	/* bucket A empty */\n	canget[s.a[2]] = 1;\n\n    for(i=0; i<3; i++)\n    for(j=0; j<3; j++)\n	search(pour(s, i, j));	\n}\n\nint main(void)\n{\n    int i;\n    FILE *fin, *fout;\n    char *sep;\n    \n    fin = stdin;\n    fout = stdout;\n\n    //fin = fopen("milk3.in", "r");\n   //fout = fopen("milk3.out", "w");\n    assert(fin != NULL && fout != NULL);\n\n    fscanf(fin, "%d %d %d", &cap[0], &cap[1], &cap[2]);\n\n    search(state(0, 0, cap[2]));\n\n    sep = "";\n    for(i=0; i<=cap[2]; i++) {\n	if(canget[i]) {\n	    fprintf(fout, "%s%d", sep, i);\n	    sep = " ";\n	}\n    }\n    fprintf(fout, "\\n");\n\n    return 0;\n}\n', 0),
(1, 8815, '/*\r\nPROG:milk3\r\nID:asiapea1\r\nLANG:C\r\n*/\r\n#include <stdio.h>\r\n#include <stdlib.h>\r\n#include <string.h>\r\n#include <assert.h>\r\n#include <ctype.h>\r\n\r\n#define MAX 20\r\n\r\ntypedef struct State	State;\r\nstruct State {\r\n    int a[3];\r\n};\r\n\r\nint seen[MAX+1][MAX+1][MAX+1];\r\nint canget[MAX+1];\r\n\r\nState\r\nstate(int a, int b, int c)\r\n{\r\n    State s;\r\n\r\n    s.a[0] = a;\r\n    s.a[1] = b;\r\n    s.a[2] = c;\r\n    return s;\r\n}\r\n\r\nint cap[3];\r\n\r\n/* pour from bucket "from" to bucket "to" */\r\nState\r\npour(State s, int from, int to)\r\n{\r\n    int amt;\r\n\r\n    amt = s.a[from];\r\n    if(s.a[to]+amt > cap[to])\r\n	amt = cap[to] - s.a[to];\r\n\r\n    s.a[from] -= amt;\r\n    s.a[to] += amt;\r\n    return s;\r\n}\r\n\r\nvoid\r\nsearch(State s)\r\n{\r\n    int i, j;\r\n\r\n    if(seen[s.a[0]][s.a[1]][s.a[2]])\r\n	return;\r\n\r\n    seen[s.a[0]][s.a[1]][s.a[2]] = 1;\r\n\r\n    if(s.a[0] == 0)	/* bucket A empty */\r\n	canget[s.a[2]] = 1;\r\n\r\n    for(i=0; i<3; i++)\r\n    for(j=0; j<3; j++)\r\n	search(pour(s, i, j));	\r\n}\r\n\r\nint main(void)\r\n{\r\n    int i;\r\n    FILE *fin, *fout;\r\n    char *sep;\r\n    \r\n    fin = stdin;\r\n    fout = stdout;\r\n\r\n    //fin = fopen("milk3.in", "r");\r\n   //fout = fopen("milk3.out", "w");\r\n    assert(fin != NULL && fout != NULL);\r\n\r\n    fscanf(fin, "%d %d %d", &cap[0], &cap[1], &cap[2]);\r\n\r\n    search(state(0, 0, cap[2]));\r\n\r\n    sep = "";\r\n    for(i=0; i<=cap[2]; i++) {\r\n	if(canget[i]) {\r\n	    fprintf(fout, "%s%d", sep, i);\r\n	    sep = " ";\r\n	}\r\n    }\r\n    fprintf(fout, "\\n");\r\n\r\n    return 0;\r\n}\r\n', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ftv_a_program`
--

CREATE TABLE IF NOT EXISTS `ftv_a_program` (
  `candidate_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `timeLimit` mediumint(9) NOT NULL,
  `memoryLimit` mediumint(9) NOT NULL,
  `lang` tinyint(4) NOT NULL,
  `status_id` tinyint(4) NOT NULL,
  `result` varchar(5000) NOT NULL,
  PRIMARY KEY (`candidate_id`,`question_id`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ftv_a_program`
--

INSERT INTO `ftv_a_program` (`candidate_id`, `question_id`, `timeLimit`, `memoryLimit`, `lang`, `status_id`, `result`) VALUES
(1, 88, 1000, 32767, 2, 1, '5,1001,988,超过时间限制,|5,1005,988,超过时间限制,|5,1058,988,超过时间限制,|5,1003,988,超过时间限制,|5,1001,988,超过时间限制,|5,1037,988,超过时间限制,|5,2000,988,超过时间限制,|5,1003,988,超过时间限制,|5,1003,988,超过时间限制,|5,1002,988,超过时间限制,|');

-- --------------------------------------------------------

--
-- Table structure for table `ftv_candidate`
--

CREATE TABLE IF NOT EXISTS `ftv_candidate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(10) NOT NULL,
  `status_id` tinyint(4) NOT NULL,
  `invite_time_int` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `start_time_int` int(11) NOT NULL,
  `end_time_int` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `extension` varchar(6) NOT NULL,
  `other` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `test_id_email` (`test_id`,`email`),
  KEY `user_id` (`user_id`),
  KEY `test_id` (`test_id`),
  KEY `email` (`email`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=170 ;

--
-- Dumping data for table `ftv_candidate`
--

INSERT INTO `ftv_candidate` (`id`, `user_id`, `test_id`, `email`, `password`, `status_id`, `invite_time_int`, `name`, `start_time_int`, `end_time_int`, `phone`, `extension`, `other`) VALUES
(152, 1, 124, 'amoa400@gmail.com', '1', 2, 1384081937, 'dasfsdaf', 1384181937, 1384281937, 'dsafdsaf', 'docx', '111111111'),
(164, 1, 124, '15@t.com', 'JM2FT0ZU6K', 1, 1384082126, '', 0, 0, '', '0', ''),
(169, 1, 124, '1@q.com', 'RHVKE3VB6D', 1, 1384082172, '', 0, 0, '', '0', '');

-- --------------------------------------------------------

--
-- Table structure for table `ftv_question`
--

CREATE TABLE IF NOT EXISTS `ftv_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `type_id` tinyint(4) NOT NULL,
  `score` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=92 ;

--
-- Dumping data for table `ftv_question`
--

INSERT INTO `ftv_question` (`id`, `user_id`, `name`, `type_id`, `score`) VALUES
(2, 1, 'sdadsafsda', 1, 5),
(3, 1, 'sdafdsaf', 1, 5),
(4, 1, 'sdafdsaf', 1, 5),
(5, 1, 'sdafdsaf', 1, 5),
(6, 1, 'sdafdsaf', 1, 5),
(7, 1, 'sdafdsaf', 1, 5),
(8, 1, 'safsdafsd', 1, 5),
(9, 1, 'ytyu', 1, 5),
(10, 1, 'safsdafsd', 1, 5),
(11, 1, 'safsdafsd', 1, 5),
(12, 1, 'safsdafsd', 1, 5),
(13, 1, '请问树上七个猴子，树', 1, 5),
(14, 1, '123456', 1, 5),
(15, 1, '123456', 1, 5),
(16, 1, '三大范德萨', 1, 5),
(18, 1, '下列房地产估价原则中适用于房地产抵押估价', 1, 5),
(19, 1, '8', 1, 5),
(20, 1, '546564', 1, 5),
(21, 1, '4645645', 1, 5),
(22, 1, '45645', 1, 5),
(23, 1, '456', 1, 5),
(24, 1, '456456', 1, 5),
(25, 1, '4565', 1, 5),
(26, 1, '456456', 1, 5),
(36, 1, '的萨菲', 1, 5),
(37, 1, '范德萨', 1, 5),
(38, 1, '的萨菲的萨菲123', 1, 1),
(39, 1, '大概的撒', 2, 10),
(43, 1, '我爱北京天安门，天安门上太阳升，啦啦啦啦', 2, 10),
(44, 1, '啊的飞洒发', 1, 10),
(45, 1, '士大夫三大啊撒的萨菲士大夫是士大夫三大啊', 2, 10),
(46, 1, '的萨菲的撒十大的萨菲的撒十大的萨菲的撒十', 3, 10),
(47, 3, '我擦', 1, 2333),
(48, 1, 'sgfdsg', 3, 10),
(50, 1, '1', 4, 20),
(52, 1, '打分', 4, 20),
(53, 1, '饭是钢', 4, 20),
(54, 1, '哈广泛地', 4, 20),
(55, 1, '的萨菲', 4, 20),
(56, 1, '富士达', 4, 20),
(57, 1, '打法', 4, 20),
(58, 1, '发个', 4, 20),
(59, 1, '发的发的萨菲的萨菲', 4, 20),
(62, 1, 'dsafdsa', 1, 10),
(63, 1, 'dafdsaf', 2, 10),
(64, 1, '453645', 1, 10),
(65, 1, '654756', 1, 10),
(66, 1, 'cxzvxczv', 1, 10),
(67, 1, 'sfdsgdf', 2, 10),
(68, 1, 'ssssssssssssssssssss', 3, 10),
(69, 1, 'sssssssssssssss', 4, 20),
(70, 1, 'fdfd', 4, 20),
(71, 1, 'd', 3, 10),
(72, 2, 'sdsf', 4, 20),
(73, 2, 'dds', 4, 20),
(74, 1, '儿童也让他', 4, 20),
(75, 1, 'fdgsfd', 1, 10),
(76, 1, 'sfdgsf', 1, 10),
(77, 1, 'dfsgdfsg', 1, 10),
(78, 1, 'ggwwwwww', 1, 10),
(79, 1, 'agasdafd', 2, 10),
(80, 1, 'ffffffffffffgfdgfgfd', 3, 10),
(81, 1, 'w', 4, 20),
(82, 1, 'fdsg', 4, 20),
(83, 1, 'sda', 4, 20),
(84, 1, 'dfsg', 4, 20),
(85, 1, 'yyyyyy', 4, 20),
(86, 1, 'f(729, 271) = _____\r', 1, 10),
(87, 3, '下列字符串中可以用作C++标示符的是', 1, 10),
(88, 1, 'USACO 1.4.4 Mother''s', 4, 20),
(89, 6, '选择最佳选项', 1, 10),
(90, 6, '说明来意', 3, 10),
(91, 6, 'www', 4, 20);

-- --------------------------------------------------------

--
-- Table structure for table `ftv_q_multi`
--

CREATE TABLE IF NOT EXISTS `ftv_q_multi` (
  `id` int(11) NOT NULL,
  `desc` varchar(1000) NOT NULL,
  `option` varchar(2000) NOT NULL,
  `answer` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ftv_q_multi`
--

INSERT INTO `ftv_q_multi` (`id`, `desc`, `option`, `answer`) VALUES
(39, '大概的撒', '123|-|$|1241|-|$|dsaf|-|$|dsafdsaf|-|$|', 'BD'),
(40, '大概的撒', '123|-|$|1241|-|$|dsaf|-|$|dsafdsaf|-|$|', 'BD'),
(41, '大概的撒', '123|-|$|1241|-|$|dsaf|-|$|dsafdsaf|-|$|', 'BD'),
(42, '大概的撒', '123|-|$|1241|-|$|dsaf|-|$|dsafdsaf|-|$|', 'BD'),
(43, '我爱北京天安门，天安门上太阳升，啦啦啦啦啦啦啦啦啦啦冷冷的拉腹蛇的艾弗森', '士大夫|-|$|的萨菲|-|$|阿斯达富士达|-|$|', 'AC'),
(45, '士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是士大夫三大啊撒的萨菲士大夫是', '范德萨|-|$|的撒|-|$|的萨菲十大|-|$|打算|-|$|打算|-|$|', 'AE'),
(63, 'dafdsaf', 'dsaf|-|$|', 'A'),
(67, 'sfdsgdf', 'sgfsdg|-|$|', 'A'),
(79, 'agasdafd', 'sdaf|-|$|ddddddddddddd|-|$|', 'AB');

-- --------------------------------------------------------

--
-- Table structure for table `ftv_q_program`
--

CREATE TABLE IF NOT EXISTS `ftv_q_program` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `desc` varchar(5000) NOT NULL,
  `time_limit` mediumint(9) NOT NULL,
  `memory_limit` mediumint(9) NOT NULL,
  `testcase` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ftv_q_program`
--

INSERT INTO `ftv_q_program` (`id`, `name`, `desc`, `time_limit`, `memory_limit`, `testcase`) VALUES
(50, '1', '22', 1000, 32767, ''),
(51, '范德萨', ' 士大夫的撒', 1000, 32767, ''),
(52, '打分', '飞的撒', 1000, 32767, ''),
(53, '饭是钢', '个粉丝', 1000, 32767, '10,6,12|'),
(54, '哈广泛地', '的法国队', 1000, 32767, ''),
(55, '的萨菲', '飞的撒', 1000, 32767, ''),
(56, '富士达', ' 十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富士达撒啊  十大富', 1000, 32767, '10,1,1|'),
(57, '打法', '十大', 1000, 32767, ''),
(58, '发个', ' 的发生过范德萨', 1000, 32767, '10,0,0|'),
(59, '发的发的萨菲的萨菲', '撒旦阿发', 1000, 32767, ''),
(60, 'sdafds', 'afdsaf', 1000, 32767, ''),
(61, 'fasdasd', 'afsdafsdaf', 1000, 32767, ''),
(69, 'sssssssssssssss', 'ss', 1000, 32767, '10,1,1|'),
(70, 'fdfd', 'gdfgdf', 1000, 32767, ''),
(72, 'sdsf', 'sdfsfdsfd', 1000, 32767, ''),
(73, 'dds', 'dsadsa', 1000, 32767, '10,6,6|'),
(74, '儿童也让他', '突然一天热', 1000, 32767, '10,0,6|'),
(81, 'w', 'fsfdg', 1000, 32767, ''),
(82, 'fdsg', 'fdsgfdg', 1000, 32767, ''),
(83, 'sda', 'fdsafdsaf', 1000, 32767, ''),
(84, 'dfsg', 'sddfsgfdsg', 1000, 32767, ''),
(85, 'yyyyyy', 'yyyyyyyyyyyyyy', 1000, 32767, ''),
(88, 'USACO 1.4.4 Mother''s Milk', '描述\r\n\r\n农民约翰有三个容量分别是A,B,C升的桶，A,B,C分别是三个从1到20的整数， 最初，A和B桶都是空的，而C桶是装满牛奶的。有时，农民把牛奶从一个桶倒到 另一个桶中，直到被灌桶装满或原桶空了。当然每一次灌注都是完全的。由于节约， 牛奶不会有丢失~~\r\n写一个程序去帮助农民找出当A桶是空的时候，C桶中牛奶所剩量的所有可能性。\r\n\r\n格式\r\n\r\nPROGRAM NAME: milk3\r\nINPUT FORMAT:\r\n(file milk3.in)\r\n单独的一行包括三个整数A,B和C。\r\nOUTPUT FORMAT:\r\n(file milk3.out)\r\n只有一行，升序地列出当A桶是空的时候，C桶牛奶所剩量的所有可能性~\r\n[编辑]SAMPLE INPUT 1\r\n\r\n8 9 10\r\n[编辑]SAMPLE OUTPUT 1\r\n\r\n1 2 8 9 10 \r\n[编辑]SAMPLE INPUT 2\r\n\r\n2 5 10 \r\n[编辑]SAMPLE OUTPUT 2\r\n\r\n5 6 7 8 9 10', 1000, 32767, '10,8,14|10,7,36|10,8,6|10,9,31|10,9,21|10,9,8|10,9,25|10,9,36|10,7,27|10,9,5|'),
(91, 'www', 'www', 1000, 32767, '');

-- --------------------------------------------------------

--
-- Table structure for table `ftv_q_qa`
--

CREATE TABLE IF NOT EXISTS `ftv_q_qa` (
  `id` int(11) NOT NULL,
  `desc` varchar(3000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ftv_q_qa`
--

INSERT INTO `ftv_q_qa` (`id`, `desc`) VALUES
(46, '的萨菲的撒十大的萨菲的撒十大的萨菲的撒十大的萨菲的撒十大的萨菲的撒十大的萨菲的撒十大的萨菲的撒十大的萨菲的撒十大的萨菲的撒十大的萨菲的撒123啊'),
(48, 'sgfdsg'),
(49, 'fdsaf'),
(68, 'ssssssssssssssssssssssssss'),
(71, 'd'),
(80, 'ffffffffffffgfdgfgfdg'),
(90, '说明来意');

-- --------------------------------------------------------

--
-- Table structure for table `ftv_q_single`
--

CREATE TABLE IF NOT EXISTS `ftv_q_single` (
  `id` int(11) NOT NULL,
  `desc` varchar(1000) NOT NULL,
  `option` varchar(2000) NOT NULL,
  `answer` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ftv_q_single`
--

INSERT INTO `ftv_q_single` (`id`, `desc`, `option`, `answer`) VALUES
(1, 'sdaf', 'sdaf|-|$|', 'A'),
(2, 'sdadsafsdaf', 'sdafsdaf|-|$|', 'A'),
(11, 'safsdafsd', 'adaf|-|$|', 'A'),
(12, 'safsdafsd', 'adaf|-|$|', 'A'),
(13, '请问树上七个猴子，树下骑个猴，总共几个猴？', '7|-|$|8|-|$|1|-|$|3|-|$|', 'B'),
(14, '123456', '1|-|$|2|-|$|3|-|$|1|-|$|', 'D'),
(15, '123456', '1|-|$|2|-|$|3|-|$|1|-|$|', 'D'),
(16, '三大范德萨', '123|-|$|412|-|$|415|-|$|2512|-|$|', 'D'),
(17, '下列关于房地产估价本质的表述中，错误的是（）。', '房地产估价是模拟市场定价而不是替代市场定价|-|$|房地产估价是提供价值意见而不是作价格保证|-|$|房地产估价会有误差且难以控制|-|$|房地产估价是评估房地产的价值而不是价格|-|$|', 'C'),
(18, '下列房地产估价原则中适用于房地产抵押估价，不适用于拆迁补偿估价的是（）。', '最高最佳使用原则|-|$|合法原则|-|$|替代原则|-|$|谨慎原则|-|$|', 'A'),
(19, '8', '98|-|$|', 'A'),
(20, '546564', '456456456|-|$|', 'A'),
(21, '4645645', '6456456|-|$|', 'A'),
(22, '45645', '456456|-|$|', 'A'),
(23, '456', '456456|-|$|', 'A'),
(24, '456456', '456|-|$|', 'A'),
(25, '4565', '45645646|-|$|', 'A'),
(26, '456456', '456456|-|$|', 'A'),
(27, '456456', '645646|-|$|', 'A'),
(28, '45645', '6456|-|$|', 'A'),
(29, '46456', '456|-|$|', 'A'),
(30, '哈让他和让他 ', '好|-|$|', 'A'),
(31, '飞', ' 撒|-|$|', 'A'),
(32, '124', '1|-|$|', 'A'),
(33, '士大夫撒', '1|-|$|2|-|$|3|-|$|4|-|$|5|-|$|', 'D'),
(34, '士大夫撒', '1|-|$|2|-|$|3|-|$|4|-|$|5|-|$|', 'D'),
(35, '士大夫撒阿萨德发阿萨德发的萨菲的萨菲十大', '1|-|$|2|-|$|3|-|$|4|-|$|5|-|$|', 'E'),
(36, '的萨菲', '十大|-|$|', 'A'),
(37, '范德萨', '十大|-|$|士大夫|-|$|爱上|-|$|敖德萨|-|$|', 'D'),
(38, '的萨菲的萨菲123', '士大夫的萨菲十大|-|$|123|-|$|的萨芬的撒阿凡达撒1|-|$|高富帅大概|-|$|阿斯达|-|$|', 'D'),
(44, '啊的飞洒发', '的撒|-|$|士大夫十大|-|$|', 'B'),
(47, '我擦', '1|-|$|2|-|$|3|-|$|4|-|$|', 'D'),
(62, 'dsafdsa', 'fdsa|-|$|', 'A'),
(64, '453645', '364536|-|$|', 'A'),
(65, '654756', 'dfgsfdsgf|-|$|', 'A'),
(66, 'cxzvxczv', 'xczv|-|$|', 'A'),
(75, 'fdgsfd', 'sgdfsg|-|$|', 'A'),
(76, 'sfdgsf', 'sgd|-|$|', 'A'),
(77, 'dfsgdfsg', 'fdsgf|-|$|', 'A'),
(78, 'ggwwwwww', 'asdg|-|$|', 'A'),
(86, 'f(729, 271) = _____\r\nint f(int x, int y) {\r\n    return (x&y)+((x^y)>>1);\r\n}\r\n', '729|-|$|450|-|$|271|-|$|500|-|$|', 'D'),
(87, '下列字符串中可以用作C++标示符的是', '_256 |-|$|5char|-|$| class|-|$|delete|-|$|', 'A'),
(89, '选择最佳选项', 'A|-|$|B|-|$|C|-|$|D|-|$|', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `ftv_test`
--

CREATE TABLE IF NOT EXISTS `ftv_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `desc` varchar(1000) NOT NULL,
  `duration` mediumint(9) NOT NULL,
  `start_datetime_int` int(11) NOT NULL,
  `end_datetime_int` int(11) NOT NULL,
  `answer_type_id` tinyint(4) NOT NULL,
  `cutoff` mediumint(9) NOT NULL,
  `allow_public` tinyint(4) NOT NULL,
  `need_info` varchar(200) NOT NULL,
  `allow_lang` varchar(200) NOT NULL,
  `count_question` mediumint(9) NOT NULL,
  `count_invited` mediumint(9) NOT NULL,
  `count_running` int(11) NOT NULL,
  `count_completed` mediumint(9) NOT NULL,
  `count_passed` int(11) NOT NULL,
  `count_failed` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=125 ;

--
-- Dumping data for table `ftv_test`
--

INSERT INTO `ftv_test` (`id`, `user_id`, `name`, `desc`, `duration`, `start_datetime_int`, `end_datetime_int`, `answer_type_id`, `cutoff`, `allow_public`, `need_info`, `allow_lang`, `count_question`, `count_invited`, `count_running`, `count_completed`, `count_passed`, `count_failed`) VALUES
(1, 1, '饿了么2014校园招聘', '这里是饿了么校园招聘', 60, 0, 0, 1, 70, 1, 'birthday|email|resume|', 'Perl|', 5, 9, -1, 5, 0, 0),
(13, 2, '饿了么2014校园招聘', '这里是饿了么校园招聘', 60, 0, 0, 1, 70, 1, 'dsafs', 'fdsafsdfa', 7, 10, 0, 5, 0, 0),
(107, 1, 'fantview2014校园招聘', '欢迎加入fantview！！！\r\n啦啦啦！', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(108, 3, 'dsb', 'xxxxxxx', 6, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(109, 3, '一场新的测评', '这是一场新的测评...', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 2, 0, 0, 0, 0, 0),
(111, 1, '啦啦啦测评！！', '卡萨解放路的撒酒疯拉卡仕达！', 60, 1384395900, 1384511400, 1, 50, 1, 'name|phone|email|graduate|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 5, 0, 0, 0, 0, 0),
(113, 4, '一场新的测评', '这是一场新的测评...', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(114, 5, '一场新的测评', '这是一场新的测评...', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(115, 5, '一场新的测评', '这是一场新的测评...', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(116, 5, '一场新的测评', '这是一场新的测评...', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(118, 3, '招一个人', '这是一场招一个人的测评', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(119, 3, '招两个人', '招两个人不行啊~~', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(120, 3, '上海维秦2014校园招聘', '上海维秦信息科技有限公司2014年校园招聘', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 4, 0, 0, 0, 0, 0),
(121, 6, '一场新的测评', '这是一场新的测评...', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 0, 0, 0, 0, 0, 0),
(122, 6, '一场新的测评', '这是一场新的测评...', 60, 0, 0, 1, 0, 1, 'name|phone|email|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 5, 0, 0, 0, 0, 0),
(124, 1, '饿了么2014校园招聘', '<strong>欢迎参加饿了么2014校园招聘，请仔细阅读以下说明：</strong>\r\n\r\n1、这是一种编程测试\r\n2、你可以自由选择从清单中的任何语言​​和代码\r\n3、如果代码区包含函数签名，只是完成的功能来看，我们会照顾的主要功能，头，等\r\n4、如果你写完整的代码，它会被提及的代码区域中。所有的输入都是从标准输入和输出到标准输出。如果你正在使用Java，使用类名作为''解决方案''', 60, 1384998300, 1385101800, 2, 0, 1, 'name|phone|resume|other|', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', 5, 2, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ftv_test_question`
--

CREATE TABLE IF NOT EXISTS `ftv_test_question` (
  `test_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `rank` smallint(6) NOT NULL,
  PRIMARY KEY (`test_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ftv_test_question`
--

INSERT INTO `ftv_test_question` (`test_id`, `question_id`, `rank`) VALUES
(13, 72, 6),
(13, 73, 7),
(109, 72, 1),
(109, 87, 2),
(111, 50, 4),
(111, 52, 1),
(111, 53, 3),
(111, 54, 2),
(111, 88, 5),
(120, 47, 3),
(120, 72, 1),
(120, 73, 2),
(120, 87, 4),
(122, 72, 4),
(122, 73, 5),
(122, 89, 1),
(122, 90, 2),
(122, 91, 3),
(124, 53, 4),
(124, 56, 3),
(124, 59, 5),
(124, 73, 2),
(124, 88, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ftv_user`
--

CREATE TABLE IF NOT EXISTS `ftv_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(32) NOT NULL,
  `type_id` tinyint(4) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ftv_user`
--

INSERT INTO `ftv_user` (`id`, `name`, `email`, `password`, `type_id`, `company_name`) VALUES
(1, '黄偲', 'amoa400@163.com', 'c3160b2e12112a0e4770a0b1347dfd6c', 1, '上海维秦信息科技有限公司'),
(2, '蒋仕龙', '89920818@qq.com', 'cba8495332f51d36003b5a0aa77ed88a', 1, ''),
(3, 'xxl', 'xxl@jzhao.cn', 'd93a5def7511da3d0f2d171d9c344e91', 1, ''),
(4, '李响', '382086919@qq.com', 'efd976b6c03ae536392d33dbd18cbcbf', 1, ''),
(5, 'BiuBiu', 'dreamer_boa@qq.com', 'a2d25d4dfd55d9e405cb6124fceac065', 1, ''),
(6, '季珉杰', '936550751@qq.com', '632c080890b32798a8006a9901a61f30', 1, ''),
(7, 'lin', 'adj@qq.com', 'b91552d5ebe6fe1829558a423d6a4c74', 100, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
