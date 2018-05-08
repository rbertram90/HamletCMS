These classes can call hooks within the system

class MyModule
{

    public function onPostCreated($params)
    {
        // do something - will be automatically run by the system
    }
}

Currently defined hooks are:
