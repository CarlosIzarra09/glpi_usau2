<?php

/**
 * ---------------------------------------------------------------------
 *
 * GLPI - Gestionnaire Libre de Parc Informatique
 *
 * http://glpi-project.org
 *
 * @copyright 2015-2022 Teclib' and contributors.
 * @copyright 2003-2014 by the INDEPNET Development Team.
 * @licence   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * ---------------------------------------------------------------------
 */

namespace tests\units\Glpi\System\Diagnostic;

class DatabaseSchemaConsistencyChecker extends \GLPITestCase
{
    protected function sqlProvider(): iterable
    {
       // `date_creation` should always be associated with `date_mod`
        yield [
            'create_table_sql'   => <<<SQL
CREATE TABLE `%s` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_creation` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
SQL
         ,
            'expected_missing'   => [
                'date_mod'
            ],
        ];
        yield [
            'create_table_sql'   => <<<SQL
CREATE TABLE `%s` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_mod` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
SQL
         ,
            'expected_missing'   => [
                'date_creation'
            ],
        ];
        yield [
            'create_table_sql'   => <<<SQL
CREATE TABLE `%s` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_mod` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
SQL
         ,
            'expected_missing'   => [],
        ];
    }

    /**
     * @dataProvider sqlProvider
     */
    public function testGetMissingFields(
        string $create_table_sql,
        array $expected_missing
    ) {

        global $DB;

        $table_name = sprintf('glpitests_%s', uniqid());

        $this->newTestedInstance($DB);
        $DB->query(sprintf($create_table_sql, $table_name));
        $missing_fields = $this->testedInstance->getMissingFields($table_name);
        $DB->query(sprintf('DROP TABLE `%s`', $table_name));

        $this->array($missing_fields)->isEqualTo($expected_missing);
    }
}
