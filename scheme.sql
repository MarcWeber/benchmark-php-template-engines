DROP TABLE IF EXISTS  BENCHMARK_RUNS;
CREATE TABLE BENCHMARK_RUNS (
  id  int(10) NOT NULL auto_increment ,
  time_setup float(8,6) NOT NULL ,
  time_case_1 float(8,6) NOT NULL ,
  time_case_2 float(8,6) NOT NULL ,

  insert_date timestamp default CURRENT_TIMESTAMP,
  cpu_info TEXT NOT NULL,
  php_version varchar(40) NOT NULL,
  n_runs int(10) NOT NULL,
  n_iterations int(10) NOT NULL,
  template_engine_name_version varchar(200) NOT NULL,
  template_engine_name_info text NOT NULL,

  html text COMMENT "the rendered HTML of this run so that users can confirm everything is ok",

  KEY (template_engine_name_version, insert_date),
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci
