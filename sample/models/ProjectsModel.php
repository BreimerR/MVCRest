<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/26/20
 * Time: 7:22 PM
 */

/**TODO
 * a model has a dataclass
 * the data class defines the columns required for the
 * model
 */

require_once "../../lib/models/Model.php";
require_once "../../lib/models/functions.php";
require_once "../../lib/data/types/functions.php";
require_once "../../lib/data/types/CompoundType.php";
require_once "../../lib/data/types/VarChar.php";
require_once "../../lib/data/types/Number.php";
require_once "../../lib/data/types/Integer.php";
require_once "../../lib/data/types/Date.php";
require_once "../data/Entity.php";
require_once "../data/Project.php";

class ProjectsModel extends Model
{

}