
CREATE TABLE `phpdebugbar` (
                               `id` varchar(55) NOT NULL,
                               `data` longblob,
                               `meta_utime` varchar(55) DEFAULT NULL,
                               `meta_datetime` varchar(55) DEFAULT NULL,
                               `meta_uri` varchar(255)  DEFAULT NULL,
                               `meta_ip` varchar(20)  DEFAULT NULL,
                               `meta_method` varchar(25) DEFAULT NULL
) ;


ALTER TABLE `phpdebugbar`
    ADD PRIMARY KEY (`id`),
  ADD KEY `meta_method` (`meta_method`),
  ADD KEY `meta_ip` (`meta_ip`),
  ADD KEY `meta_uri` (`meta_uri`),
  ADD KEY `meta_utime` (`meta_utime`),
  ADD KEY `meta_datetime` (`meta_datetime`);
