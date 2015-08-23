<?php


	include("configuracion.php");
	
	ob_start();
    $enlace = mysqli_connect($db_host, $db_user, $db_password);
    ob_end_clean();
	
    /* Verify the connection */
    if (mysqli_connect_errno()) {
        $message = utf8_encode("Error in the connection: ".mysqli_connect_error());	
        $response['code'] = "001"; // code 001 error de conexión
        $response['message'] = $message; 
        die(json_encode($response));
    }

	$query ="SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `db_jmeteranalyzer` ;

CREATE SCHEMA IF NOT EXISTS `db_jmeteranalyzer` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `db_jmeteranalyzer` ;

DROP TABLE IF EXISTS `db_jmeteranalyzer`.`tbl_tests` ;

CREATE TABLE IF NOT EXISTS `db_jmeteranalyzer`.`tbl_tests` (
  `id_test` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  `starttime` DATETIME NOT NULL COMMENT '',
  `finishtime` DATETIME NOT NULL COMMENT '',
  `duration` VARCHAR(45) NOT NULL COMMENT '',
  `transaccount` BIGINT(13) NOT NULL COMMENT '',
  `minRT` FLOAT NOT NULL COMMENT '',
  `maxRT` FLOAT NOT NULL COMMENT '',
  `avgRT` FLOAT NOT NULL COMMENT '',
  `avgTPS` FLOAT NOT NULL COMMENT '',
  `maxTPS` FLOAT NOT NULL COMMENT '',
  `numberoffiles` INT NOT NULL COMMENT '',
  `multifile` TINYINT(1) NOT NULL COMMENT '',
  PRIMARY KEY (`id_test`)  COMMENT '',
  UNIQUE INDEX `tbl_testscol_UNIQUE` (`name` ASC)  COMMENT '',
  UNIQUE INDEX `idtbl_tests_UNIQUE` (`id_test` ASC)  COMMENT '')
ENGINE = InnoDB;

DROP TABLE IF EXISTS `db_jmeteranalyzer`.`tbl_responsecodes` ;

CREATE TABLE IF NOT EXISTS `db_jmeteranalyzer`.`tbl_responsecodes` (
  `tbl_tests_id_test` INT NOT NULL COMMENT '',
  `id_responsecodes` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `respcodecode` VARCHAR(50) NULL COMMENT '',
  `numberofresponses` INT NOT NULL COMMENT '',
  `label` VARCHAR(345) NOT NULL COMMENT '',
  INDEX `fk_tbl_responsecodes_tbl_tests1_idx` (`tbl_tests_id_test` ASC)  COMMENT '',
  PRIMARY KEY (`id_responsecodes`)  COMMENT '',
  CONSTRAINT `fk_tbl_responsecodes_tbl_tests1`
    FOREIGN KEY (`tbl_tests_id_test`)
    REFERENCES `db_jmeteranalyzer`.`tbl_tests` (`id_test`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

DROP TABLE IF EXISTS `db_jmeteranalyzer`.`tbl_percentiles` ;

CREATE TABLE IF NOT EXISTS `db_jmeteranalyzer`.`tbl_percentiles` (
  `tbl_tests_id_test` INT NOT NULL COMMENT '',
  `id_percentiles` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `label` VARCHAR(345) NOT NULL COMMENT '',
  `percentil` VARCHAR(3) NOT NULL COMMENT '',
  `responsetime` FLOAT NOT NULL COMMENT '',
  `resptype` VARCHAR(3) NOT NULL COMMENT '',
  INDEX `fk_tbl_percentiles_tbl_tests_idx` (`tbl_tests_id_test` ASC)  COMMENT '',
  PRIMARY KEY (`id_percentiles`)  COMMENT '',
  CONSTRAINT `fk_tbl_percentiles_tbl_tests`
    FOREIGN KEY (`tbl_tests_id_test`)
    REFERENCES `db_jmeteranalyzer`.`tbl_tests` (`id_test`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

DROP TABLE IF EXISTS `db_jmeteranalyzer`.`tbl_files` ;

CREATE TABLE IF NOT EXISTS `db_jmeteranalyzer`.`tbl_files` (
  `tbl_tests_id_test` INT NOT NULL COMMENT '',
  `id_files` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  `server` VARCHAR(15) NOT NULL COMMENT '',
  PRIMARY KEY (`id_files`)  COMMENT '',
  INDEX `tbl_tests_id_test` (`tbl_tests_id_test` ASC)  COMMENT '',
  CONSTRAINT `fk_tbl_files_tbl_tests1`
    FOREIGN KEY (`tbl_tests_id_test`)
    REFERENCES `db_jmeteranalyzer`.`tbl_tests` (`id_test`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

DROP TABLE IF EXISTS `db_jmeteranalyzer`.`tbl_labels` ;

CREATE TABLE IF NOT EXISTS `db_jmeteranalyzer`.`tbl_labels` (
  `tbl_tests_id_test` INT NOT NULL COMMENT '',
  `id_labels` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `label` VARCHAR(345) NULL COMMENT '',
  INDEX `fk_tbl_labels_tbl_tests1_idx` (`tbl_tests_id_test` ASC)  COMMENT '',
  PRIMARY KEY (`id_labels`)  COMMENT '',
  CONSTRAINT `fk_tbl_labels_tbl_tests1`
    FOREIGN KEY (`tbl_tests_id_test`)
    REFERENCES `db_jmeteranalyzer`.`tbl_tests` (`id_test`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
";

	$result = mysqli_multi_query($enlace, $query);
	
	if ($result){
		$response['code'] = "000";
		$response['message'] = "DB configured successfully.";//"The test: ".$testname." has been created successfully, the next step is to upload the test logs";
	}else{
		$response['code'] = "XXX";
		$response['message'] = "Error in the query INSTALL: ".mysqli_error($enlace)."<br>".$query;
		die(json_encode($response));
	}
	
	mysqli_close($enlace);
	echo json_encode($response);