<?php

class AlterTableSQLRewriter extends AbstractSQLRewriter
{
    private $stringReplacements = [
        'bigint(20)'	=> 'bigint',
        'bigint(10)'	=> 'int',
        'int(11)'		=> 'int',
        'tinytext'		=> 'text',
        'mediumtext'	=> 'text',
        'longtext'		=> 'text',
        'unsigned'		=> '',
        'gmt datetime NOT NULL default \'0000-00-00 00:00:00\''	=> 'gmt timestamp NOT NULL DEFAULT timezone(\'gmt\'::text, now())',
        'default \'0000-00-00 00:00:00\''	=> 'DEFAULT now()',
        '\'0000-00-00 00:00:00\''	=> 'now()',
        'datetime'		=> 'timestamp',
        'DEFAULT CHARACTER SET utf8'	=> '',

        // WP 2.7.1 compatibility
        'int(4)'		=> 'smallint',

        // For WPMU (starting with WP 3.2)
        'tinyint(2)'	=> 'smallint',
        'tinyint(1)'	=> 'smallint',
        "enum('0','1')"	=> 'smallint',
        'COLLATE utf8_general_ci'	=> '',

        // For flash-album-gallery plugin
        'tinyint'		=> 'smallint'
    ];

    public function rewrite(): string
    {
        $sql = $this->original();

        if (str_contains($sql, 'CHANGE COLUMN')) {
            $sql = $this->rewriteChangeColumn($sql);
        }
        if (str_contains($sql, 'ALTER COLUMN')) {
            $sql = $this->rewriteAlterColumn($sql);
        }
        if (str_contains($sql, 'ADD COLUMN')) {
            $sql = $this->rewriteAddColumn($sql);
        }
        if (str_contains($sql, 'ADD KEY') || str_contains($sql, 'ADD UNIQUE KEY')) {
            $sql = $this->rewriteAddKey($sql);
        }
        if (str_contains($sql, 'DROP INDEX')) {
            $sql = $this->rewriteDropIndex($sql);
        }
        if (str_contains($sql, 'DROP PRIMARY KEY')) {
            $sql = $this->rewriteDropPrimaryKey($sql);
        }

        return $sql;
    }

    private function rewriteChangeColumn(string $sql): string
    {
        $pattern = '/ALTER TABLE\s+(\w+)\s+CHANGE COLUMN\s+([^\s]+)\s+([^\s]+)\s+([^ ]+)( unsigned|)\s*(NOT NULL|)\s*(default (.+)|)/';
        
        if(1 === preg_match($pattern, $sql, $matches)) {
            $table = $matches[1];
            $col = $matches[2];
            $newname = $matches[3];
            $type = strtolower($matches[4]);
            if(isset($this->stringReplacements[$type])) {
                $type = $this->stringReplacements[$type];
            }
            $unsigned = $matches[5];
            $notnull = $matches[6];
            $default = $matches[7];
            $defval = $matches[8];
            if(isset($this->stringReplacements[$defval])) {
                $defval = $this->stringReplacements[$defval];
            }
            $newq = "ALTER TABLE $table ALTER COLUMN $col TYPE $type";
            if(!empty($notnull)) {
                $newq .= ", ALTER COLUMN $col SET NOT NULL";
            }
            if(!empty($default)) {
                $newq .= ", ALTER COLUMN $col SET DEFAULT $defval";
            }
            if($col != $newname) {
                $newq .= ";ALTER TABLE $table RENAME COLUMN $col TO $newcol;";
            }
            $sql = $newq;
        }

        return $sql;
    }

    private function rewriteAlterColumn(string $sql): string
    {
        $pattern = '/ALTER TABLE\s+(\w+)\s+ALTER COLUMN\s+/';

        if(1 === preg_match($pattern, $sql)) {
            // Translate default values
            $sql = str_replace(
                array_keys($this->stringReplacements),
                array_values($this->stringReplacements),
                $sql
            );
        }

        return $sql;
    }

    private function rewriteAddColumn(string $sql): string
    {
        $pattern = '/ALTER TABLE\s+(\w+)\s+ADD COLUMN\s+([^\s]+)\s+([^ ]+)( unsigned|)\s+(NOT NULL|)\s*(default (.+)|)/';

        if(1 === preg_match($pattern, $sql, $matches)) {
            $table = $matches[1];
            $col = $matches[2];
            $type = strtolower($matches[3]);
            if(isset($this->stringReplacements[$type])) {
                $type = $this->stringReplacements[$type];
            }
            $unsigned = $matches[4];
            $notnull = $matches[5];
            $default = $matches[6];
            $defval = $matches[7];
            if(isset($this->stringReplacements[$defval])) {
                $defval = $this->stringReplacements[$defval];
            }
            $newq = "ALTER TABLE $table ADD COLUMN $col $type";
            if(!empty($default)) {
                $newq .= " DEFAULT $defval";
            }
            if(!empty($notnull)) {
                $newq .= " NOT NULL";
            }
            $sql = $newq;
        }

        return $sql;
    }

    private function rewriteAddKey(string $sql): string
    {
        $pattern = '/ALTER TABLE\s+(\w+)\s+ADD (UNIQUE |)KEY\s+([^\s]+)\s+\(((?:[^\(\)]+|\([^\(\)]+\))+)\)/';

        if(1 === preg_match($pattern, $sql, $matches)) {
            $table = $matches[1];
            $unique = $matches[2];
            $index = $matches[3];
            $columns = $matches[4];

            // Remove prefix indexing
            // Rarely used and apparently unnecessary for current uses
            $columns = preg_replace('/\([^\)]*\)/', '', $columns);

            // Workaround for index name duplicate
            $index = $table . '_' . $index;
            $sql = "CREATE {$unique}INDEX $index ON $table ($columns)";
        }

        return $sql;
    }

    private function rewriteDropIndex(string $sql): string
    {
        $pattern = '/ALTER TABLE\s+(\w+)\s+DROP INDEX\s+([^\s]+)/';

        if(1 === preg_match($pattern, $sql, $matches)) {
            $table = $matches[1];
            $index = $matches[2];
            $sql = "DROP INDEX ${table}_${index}";
        }

        return $sql;
    }

    private function rewriteDropPrimaryKey(string $sql): string
    {
        $pattern = '/ALTER TABLE\s+(\w+)\s+DROP PRIMARY KEY/';

        if(1 === preg_match($pattern, $sql, $matches)) {
            $table = $matches[1];
            $sql = "ALTER TABLE ${table} DROP CONSTRAINT ${table}_pkey";
        }

        return $sql;
    }
}
