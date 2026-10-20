<?php

declare(strict_types=1);

use App\Core\Database;
// Use Database::connection() wherever a controller/service needs a DB connection.
// The connection is lazy, so pages that do not use the database can load before
// the local database has been configured.
return static function (): \PDO {
    return Database::connection();
};
