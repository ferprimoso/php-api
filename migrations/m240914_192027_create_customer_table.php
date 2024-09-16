<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer}}`.
 */
class m240914_192027_create_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'cpf' => $this->string(11)->notNull()->unique(),
            'cep' => $this->string(8)->notNull(),
            'street' => $this->string()->notNull(),
            'number' => $this->string()->notNull(),
            'city' => $this->string()->notNull(),
            'state' => $this->string(2)->notNull(),  // Assuming state abbreviation (e.g., 'SP' for São Paulo)
            'additional_information' => $this->string(),
            'sex' => $this->char(1)->notNull(), // M/F
        ]);

          // Index for the name field (used for searching and sorting)
        $this->createIndex(
            'idx-customer-name',
            '{{%customer}}',
            'name'
        );

        // Index for the city field (used for filtering by city)
        $this->createIndex(
            'idx-customer-city',
            '{{%customer}}',
            'city'
        );

        // Index for the state field (used for filtering by state)
        $this->createIndex(
            'idx-customer-state',
            '{{%customer}}',
            'state'
        );


        // Add index for CPF to improve search performance
        $this->createIndex(
            'idx-customer-cpf',
            '{{%customer}}',
            'cpf'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        // Drop indexes before dropping the table
        $this->dropIndex('idx-customer-name', '{{%customer}}');
        $this->dropIndex('idx-customer-city', '{{%customer}}');
        $this->dropIndex('idx-customer-state', '{{%customer}}');
        $this->dropIndex('idx-customer-cpf', '{{%customer}}');

        $this->dropTable('{{%customer}}');
    }
}
