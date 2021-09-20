<?php
use Migrations\AbstractMigration;

class ChangeToMbstring extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->execute('ALTER DATABASE phanto41_ether CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->execute('ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->execute('ALTER TABLE `users` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->execute('ALTER TABLE `thoughts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->execute('ALTER TABLE `thoughts` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->execute('ALTER TABLE `comments` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->execute('ALTER TABLE `comments` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->execute('ALTER TABLE `messages` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->execute('ALTER TABLE `messages` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
    }
}
