This is an ongoing project, some features may not be fully complete or documented.

`TODO` 
** updates to core classes to make use of traits/interfaces/abstract approaches **

You can set your `DOCUMENT_ROOT` to the base of your project or to a specific project under `apps/{projectName}`

It is recommended, you set your `DOCUMENT_ROOT` to a particular app as to not expose your configuration directory.

To run the sample-project, run `composer install` and install the sql file `sample.sql`

`CONFIG`

path: /config/{appName}.json
content:
```

{
    "database" : {
        "live" : {
            "host" : {
              "alias" : "sample",
              "write" : "127.0.0.1",
              "read" : "127.0.0.1"
            },
            "name" : "sample",
            "user" : "root",
            "pass" : "root"
        },
        "staging" : {
            "alias" : "sample",
            "host" : "127.0.0.1",
            "name" : "sample",
            "user" : "root",
            "pass" : "root"
        },
        "dev" : {
            "alias" : "sample",
            "host" : "127.0.0.1",
            "name" : "sample",
            "user" : "root",
            "pass" : "root"
        }
    }
}
```

In the event that you have specified endpoints for a read or write operations, you can specify the connection parameters for those operations.

`alias` this is the reference that you will use when establishing queries.

`APP`

path: /apps/{appName}

each app needs a `controllers` directory in order for it to be registered.

`ROUTING`

format: `/controller/action/param1/param2/ .. /`

sample: `/home`

sample: `/users/view/1`

you can also force a route before you load a project in your DOCUMENT_ROOT index.php

```
// Controller and Action are implied when specifying custom routes
$jinxup->route('/login')->to('Auth', 'login');
$jinxup->route('/logout')->to('Auth', 'logout');
$jinxup->route('/forgot-password')->to('Auth', 'forgot_password');
$jinxup->route('/reset-password')->to('Auth', 'reset_password');
```


`CONTROLLER`

```
<?php
  class Home_Controller {
  
    public function indexAction() {
    
      echo 'this is the entry point for the controller';
    }
  }
?>
```

`CALLING models, helpers`

file: /apps/{app}/controllers/users.php
 ```
 <?php
 
  class Users_Controller extends Jinxup {
  
    public function viewAction($id) {
    
      // using a helper class access user data
      $user = $this->helper->user->fetch($id);
      
      // using model class to access user data
      $user = $this->model->user->fetch($id);
    }
  }
 ```
 
 `MODELS`
 
 file: /apps/{app}/models/user.php
 
 ```
 <?php
  class User_Model extends Jinxup {
  
    public function fetch($id) {
      
      // remember the "alias" reference in the config? you would utilize it here
      $user = $this->db->sample('select * from users where user_id = :id', ['id' => $id);
      
      return count($user) > 0 ? $user[0] : false;
    }
  }
 ```
 
  
 `HELPERS`
 
 file: /apps/{app}/helpers/user.php
 
 ```
 <?php
  class User_Helpers extends Jinxup {
  
    public function fetch($id) {
      
      $user = ['user_id' => $id];
      
      return $user;
    }
  }
 ```
