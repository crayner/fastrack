--
-- Table structure for table `#__fastrack_files`
--

CREATE TABLE IF NOT EXISTS `#__fastrack_files` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `path` text NOT NULL,
  `resultPath` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fastrack File Definitions';

--
-- Indexes for table `#__fastrack_files`
--

ALTER TABLE `#__fastrack_files`
  ADD PRIMARY KEY (`id`), 
  ADD UNIQUE KEY `name` (`name`(25));