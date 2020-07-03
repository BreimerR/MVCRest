<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/26/20
 * Time: 7:25 PM
 */

class Project extends CompoundType
{
    /**
     * @param string $name
     * @param Date $date
     * @param int $group
     * @param int $creator
     * @param int $documentation
     */
    public function __construct($name, $date, $group, $creator, $documentation)
    {
        parent::__construct(self::mapParams($date, $name, $group, $creator));
    }

    static function initColumns(): array
    {

        $name = new VarChar("name", 150);

        return [
            new Date("date", ""),
            new VarChar("name", 255, "John Doe"),
            new Integer("group", 255),
            self::registerForeignKey("entity", Entity::class),
            new Integer("creator", 255)
        ];
    }

}


