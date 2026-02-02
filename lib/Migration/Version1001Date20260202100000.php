<?php

declare(strict_types=1);

namespace OCA\Ereader\Migration;

use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1001Date20260202100000 extends SimpleMigrationStep {

	public function __construct(
		private \OCP\IDBConnection $db,
	) {
	}

	public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options) {
		/** @var \OCP\DB\ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('ereader_dictionary')) {
			$table = $schema->createTable('ereader_dictionary');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('word', Types::STRING, [
				'notnull' => true,
				'length' => 512,
			]);
			$table->addColumn('translation', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('created_at', Types::DATETIME, [
				'notnull' => true,
			]);
			$table->addColumn('updated_at', Types::DATETIME, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['user_id', 'word'], 'ereader_dictionary_user_word');
		}

		return $schema;
	}
}
