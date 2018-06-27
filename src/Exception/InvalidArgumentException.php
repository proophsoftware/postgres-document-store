<?php
/**
 * This file is part of the proophsoftware/postgres-document-store.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventMachine\Postgres\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements PostgresDocumentStoreException
{
}
