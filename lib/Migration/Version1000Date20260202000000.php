<?php

declare(strict_types=1);

namespace OCA\Ereader\Migration;

use OCP\DB\Types;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1000Date20260202000000 extends SimpleMigrationStep {

	public function __construct(
		private IDBConnection $db,
	) {
	}

	public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options) {
		/** @var \OCP\DB\ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('ereader_progress')) {
			$table = $schema->createTable('ereader_progress');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('file_id', Types::BIGINT, [
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('progress_data', Types::TEXT, [
				'notnull' => true,
			]);
			$table->addColumn('updated_at', Types::DATETIME, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['user_id', 'file_id'], 'ereader_progress_user_file');
		}

		if (!$schema->hasTable('ereader_bookmarks')) {
			$table = $schema->createTable('ereader_bookmarks');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('file_id', Types::BIGINT, [
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('position', Types::TEXT, [
				'notnull' => true,
			]);
			$table->addColumn('note', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('created_at', Types::DATETIME, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id', 'file_id'], 'ereader_bookmarks_user_file');
		}

		return $schema;
	}
}
