<?php
/**
 * DB.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP;

use Ashleyfae\MigrationsWP\Exceptions\DatabaseQueryException;

/**
 * Static wrapper for `\wpdb`.
 * Taken from GiveWP.
 *
 * @method static int|bool query(string $query)
 * @method static int|false insert(string $table, array $data, array|string $format)
 * @method static int|false delete(string $table, array $where, array|string $where_format)
 * @method static int|false update(string $table, array $data, array $where, array|string $where_format)
 * @method static int|false replace(string $table, array $data, array|string $format)
 * @method static null|string get_var(string $query = null, int $x = 0, int $y = 0)
 * @method static array|object|null|void get_row(string $query = null, string $output = OBJECT, int $y = 0)
 * @method static array get_col(string $query = null, int $x = 0)
 * @method static array|object|null get_results(string $query = null, string $output = OBJECT)
 * @method static string get_charset_collate()
 */
class DB
{

    /**
     * Runs the dbDelta function.
     *
     * @see dbDelta()
     *
     * @param  string  $delta
     *
     * @return array
     * @throws DatabaseQueryException
     */
    public static function delta(string $delta): array
    {
        return self::runQueryWithErrorChecking(
            function () use ($delta) {
                return dbDelta($delta);
            }
        );
    }

    public static function prepare($query, ...$args)
    {
        global $wpdb;

        return $wpdb->prepare($query, ...$args);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::runQueryWithErrorChecking(
            function () use ($name, $arguments) {
                global $wpdb;

                return call_user_func_array([$wpdb, $name], $arguments);
            }
        );
    }

    /**
     * Get last insert ID
     *
     * @since 1.0
     * @return int
     */
    public static function last_insert_id(): int
    {
        global $wpdb;
        return $wpdb->insert_id;
    }

    /**
     * Runs a query callable and checks to see if any unique SQL errors occurred when it was run
     *
     * @since 1.0
     *
     * @param  Callable  $queryCaller
     *
     * @return mixed
     * @throws DatabaseQueryException
     */
    private static function runQueryWithErrorChecking(callable $queryCaller)
    {
        global $wpdb, $EZSQL_ERROR;
        require_once ABSPATH.'wp-admin/includes/upgrade.php';

        $errorCount    = is_array($EZSQL_ERROR) ? count($EZSQL_ERROR) : 0;
        $hasShowErrors = $wpdb->hide_errors();

        $output = $queryCaller();

        if ($hasShowErrors) {
            $wpdb->show_errors();
        }

        $wpError = self::getQueryErrors($errorCount);

        if (! empty($wpError->errors)) {
            throw DatabaseQueryException::create($wpError->get_error_messages());
        }

        return $output;
    }


    /**
     * Retrieves the SQL errors stored by WordPress
     *
     * @since 1.0
     *
     * @param  int  $initialCount
     *
     * @return \WP_Error
     */
    private static function getQueryErrors(int $initialCount = 0): \WP_Error
    {
        global $EZSQL_ERROR;

        $wpError = new \WP_Error();

        if (is_array($EZSQL_ERROR)) {
            for ($index = $initialCount, $indexMax = count($EZSQL_ERROR); $index < $indexMax; $index++) {
                $error = $EZSQL_ERROR[$index];

                if (
                    empty($error['error_str']) ||
                    empty($error['query']) ||
                    strpos($error['query'], 'DESCRIBE ') === 0
                ) {
                    continue;
                }

                $wpError->add('db_delta_error', $error['error_str']);
            }
        }

        return $wpError;
    }

}
