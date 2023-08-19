
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


CREATE TABLE `phpdebugbar_sql` (
                                   `id` int NOT NULL,
                                   `requestId` varchar(55) NOT NULL,
                                   `query` text NOT NULL,
                                   `params` text NOT NULL,
                                   `duration` varchar(25)  NOT NULL,
                                   `duration_str` varchar(25)  NOT NULL
);

ALTER TABLE `phpdebugbar_sql`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `phpdebugbar_sql`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;
