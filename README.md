Simsimi php sdk
============
simsimi sdk with no auth.

Usage
----------
```
include("simsimi.class.php");
$name = "suckit";
$content = "hi";
$sim = new Simsimi(array('sid'=>$name,'datapath'=>'sim_','proxy'=>'http://your proxy ip:8080'));
$ready = $sim->init();
$result = $sim->talk($content);
echo $result;

```
if you don't use proxy, you can omit the proxy option. sid option is the session handle keeping the dialog.  

License
-------
This is licensed under the GNU LGPL, version 2.1 or later.   
For details, see: http://creativecommons.org/licenses/LGPL/2.1/