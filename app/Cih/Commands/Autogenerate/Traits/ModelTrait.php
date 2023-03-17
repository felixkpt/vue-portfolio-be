<?php

namespace App\Cih\Commands\Autogenerate\Traits;

use Illuminate\Support\Str;

trait ModelTrait
{

    function promptModel($is_create_model = false, $prompt_namespace = true, $prompt_fields = true)
    {
        // Force Model specify if is create model
        if ($is_create_model === true && !$this->autoGenerateProps->default_name) {
            $model_name = '';
            while (!preg_match('#[a-z0-9]#i', $model_name))
                $model_name = Str::studly($this->ask("What is the name of the model? A to abort"));
        } else {
            $model = '';
            if ($this->autoGenerateProps->default_name)
                $model = Str::studly(Str::singular($this->autoGenerateProps->default_name));
            $model_name = Str::studly($this->ask("What is the name of the model? A to abort", $model ? $model : ''), $model);
        }

        if (strtolower($model_name) == 'a') return false;


        if ($prompt_namespace === true) {
            $subfolder = $this->ask("What is the model subfolder? A to abort", "Core");
            if (strtolower($subfolder) == 'a') return false;

            if ($subfolder) {
                $parts = explode('/', $subfolder);
                $subfolder = array_reduce($parts, fn ($prev, $curr) => $prev . '/' . Str::studly($curr), '');
            }
            $this->autoGenerateProps->set('model_folder', $subfolder);
        }

        if ($model_name)
            $this->setModel($model_name);
    }

    public function getModelFields($is_create_model = false)
    {

        if ($is_create_model === true && $this->autoGenerateProps->recently_created_migration) {
            if ($this->confirm("You recently created a migration, copy it's fields?", 'Yes')) return true;
        }

        $this->info($is_create_model === true ? "Start adding model fillables" : "Start adding migration fields");

        $plain_fields = [];
        $fields = [];
        $add_more = 1;
        $columntypes =
            [
                'string',
                'unsignedBigInteger',
                'boolean',
                'bigInteger',
                'unsignedInteger',
                'integer',
                'tinyInteger',
                'smallInteger',
                'mediumInteger',
                'char',
                'date',
                'dateTime',
                'text',
                'longText',
                'json',
                'bigIncrements',
                'binary',
                'char',
                'decimal',
                'double',
                'enum',
                'float',
                'increments',
                'mediumText',
                'morphs',
                'nullableTimestamps',
                'softDeletes',
                'time',
                'timestamp',
                'timestamps',
                'rememberToken',
            ];

        while ($add_more) {

            $field_name = '';
            $invalid = true;
            while ($invalid) {

                $field_name = $this->ask("Add new field, N to end");

                if (strlen($field_name) > 0) {

                    $field_name = Str::slug(Str::kebab(strtolower($field_name)), '_');

                    $invalid = in_array($field_name, $columntypes);

                    if ($invalid === true) {
                        $accepted = $this->confirm("Whoops! " . $field_name . " looks invalid, use it anyway?");

                        if ($accepted === true) $invalid = false;
                        else $field_name = '';
                    }
                }
            }

            if (strtolower($field_name) == 'n') {
                $add_more = 0;
            } else {

                if ($is_create_model === false) {

                    $key = 0;
                    $str = rtrim(trim(array_reduce(
                        $columntypes,
                        function ($prev, $curr) use (&$key) {
                            $key++;
                            $separator = ', ';
                            return $prev . $curr . '[' . $key . ']' . $separator;
                        },
                        ''
                    )), ',');

                    // guessed columntypes
                    $guessed = [
                        'name' => 'string', 'age' => 'integer',
                        'created_by' => 'unsignedBigInteger',
                        'id' => 'unsignedBigInteger',
                        '*_id' => 'unsignedBigInteger',
                        '*phone*' => 'unsignedBigInteger',
                        'is_*' => 'boolean',
                        'status' => 'boolean',
                        'content' => 'longText',
                        'date' => 'date', 'date_time' => 'dateTime', 'time' => 'time'
                    ];

                    $try = preg_match("#_id$#", $field_name) ? '*_id' : $field_name;
                    $default = $guessed[$try] ?? 'string';

                    if (preg_match("#_id$#", $field_name))
                        $default = $guessed['*_id'];

                    if (preg_match("#^is_#", $field_name))
                        $default = $guessed['is_*'];

                    if (preg_match("#^phone|phone$#", $field_name))
                        $default = $guessed['*phone*'];


                    $stop = false;
                    $msg = '';
                    while (!$stop) {
                        $field_type = $this->ask(($msg ? $msg : "Type/choose Column type") . " (" . $str . ")", $default);

                        if (is_numeric($field_type) > 0 && $field_type > 0) {
                            $field_type = $columntypes[$field_type - 1] ?? 'string';
                            $stop = true;
                        } else if (false !== array_search(strtolower($field_type), array_map('strtolower', $columntypes))) $stop = true;
                        $msg = 'Unknown Column type!';
                    }

                    $field_type = str_replace('datetime', 'dateTime', $field_type);
                    $field_type = str_replace('longtext', 'longText', $field_type);
                    $field_type = str_replace('unsignedbiginteger', 'unsignedBigInteger', $field_type);

                    $fields[] = [
                        'name' => $field_name,
                        'type' => $field_type
                    ];
                }

                $plain_fields[] = $field_name;
            }
        }

        $this->autoGenerateProps->set('fields', $fields);
        $this->autoGenerateProps->set('plain_fields', $plain_fields);
        $this->autoGenerateProps->set('model_fields', '"' . implode('" , "', $this->autoGenerateProps->plain_fields) . '"');
    }
}
