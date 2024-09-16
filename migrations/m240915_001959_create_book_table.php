<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m240915_001959_create_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'isbn' => $this->string(13)->notNull(),
            'title' => $this->string()->notNull(),
            'author' => $this->string()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'stock' => $this->integer()->notNull(),
        ]);
        
         // Create indexes for optimized querying

        // Index for the title field (used for sorting/filtering)
        $this->createIndex(
            'idx-book-title',
            '{{%book}}',
            'title'
        );

        // Index for the author field (used for filtering)
        $this->createIndex(
            'idx-book-author',
            '{{%book}}',
            'author'
        );

        // Index for the price field (used for sorting)
        $this->createIndex(
            'idx-book-price',
            '{{%book}}',
            'price'
        );
    }
    

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop the indexes before dropping the table
        $this->dropIndex('idx-book-title', '{{%book}}');
        $this->dropIndex('idx-book-author', '{{%book}}');
        $this->dropIndex('idx-book-price', '{{%book}}');

        $this->dropTable('{{%book}}');
    }
}
