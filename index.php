<?php
 require_once 'jsondb/bootstrap.php';
 define('JSONDB_DATA_PATH', realpath(dirname(__FILE__)).'/data/'); //Path to folder with tables

use \jsondb\classes\JSONDB as JSONDB;

$find_all = JSONDB::factory('users')->find_all();

 $count_all = JSONDB::factory('users')->count();
 $count_group = JSONDB::factory('users')->group_by('name')->count();

 $where[] = JSONDB::factory('users')->where('id', '>', 13)->find_all();
 $where[] = JSONDB::factory('users')->where('name', '=', 'gerg0sz')->find_all();
 $where[] = JSONDB::factory('users')->where('id', 'IN', array(1, 14, 24, 25, 30))->find_all();
 $where[] = JSONDB::factory('users')->where('id', '>', 18)->and_where('id', '<', 24)->find_all();
 $where[] = JSONDB::factory('users')->where('name', '=', 'gerg0sz')->or_where('id', '>', 21)->and_where('id', '<=', 29)->find_all();

 $order = JSONDB::factory('users')->order_by('name')->order_by('id')->find_all();

 $limit[] = JSONDB::factory('users')->limit(5)->find_all();
 $limit[] = JSONDB::factory('users')->limit(5, 4)->find_all();

 $array[] = JSONDB::factory('users')->as_array(null, 'name')->find_all();
 $array[] = JSONDB::factory('users')->group_by('name')->as_array('id', 'name')->find_all();
 $array[] = JSONDB::factory('users')->group_by('name')->as_array('name', 'id')->find_all();
?>


<style>
    div {font-family: Arial; color: #4c4c4c;}
    pre {font-family: Consolas, Courier New;}
    h3,h4,h5 {color: #000;}
    .query {
        overflow:auto; 
        border: 1px dashed #d0d0d0;
        padding: 5px;
        font-size: 13px;
        background: #4c4c4c; 
        color: #fff;
    }
    .result {
        overflow:auto; 
        max-height: 300px;
        border: 1px solid #d0d0d0;
        padding: 5px;
        background: #4c4c4c; 
        color: #fff;
    }
    a {
        color: #4c4c4c;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
<div>
    <h3 id="menu">Examples</h3>
    <ul>
        <li><a href="#find_all">Find all</a></li>
        <li><a href="#count">Count</a></li>
        <li><a href="#where">Where</a></li>
        <li><a href="#order">Order By</a></li>
        <li><a href="#limit">Limit</a></li>
        <li><a href="#as_array">As Array</a></li>
    </ul>

    <div id="find_all">
        <h3>Find all <a href="#menu">&uparrow;</a></h3> 
        <ol>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($find_all, true) ?></pre>
            </li>
        </ol>
    </div>
    <hr>
    <div id="count">
        <h3>Count <a href="#menu">&uparrow;</a></h3> 
        <ol>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->count();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($count_all, true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->group_by('name')->count();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($count_group, true) ?></pre>
            </li>
        </ol>
    </div>
    <hr>
    <div id="where">
        <h3>Where <a href="#menu">&uparrow;</a></h3> 
        <ol>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->where('id', '>', 13)->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($where[0], true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->where('name', '=', 'gerg0sz')->find_all();</pre>

                <h5>Result:</h5> <pre class="result"><?= print_r($where[1], true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->where('id', 'IN', array(1, 14, 24, 25, 30))->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($where[2], true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->where('id', '>', 18)->and_where('id', '<', 24)->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($where[3], true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->where('name', '=', 'gerg0sz')->or_where('id', '>', 21)->and_where('id', '<=', 29)->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($where[4], true) ?></pre>
            </li>
        </ol>
    </div>
    <hr>
    <div id="order">
        <h3>Order By <a href="#menu">&uparrow;</a></h3> 
        <ol>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->order_by('name')->order_by('id')->find_all()</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($order, true) ?></pre>
            </li>
        </ol>
    </div>
    <div id="limit">
        <h3>Limit <a href="#menu">&uparrow;</a></h3> 
        <ol>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->limit(5)->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($limit[0], true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->limit(5, 4)->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($limit[1], true) ?></pre>
            </li>
        </ol>
    </div>
    <hr>
    <div id="as_array">
        <h3>As Array <a href="#menu">&uparrow;</a></h3> 
        <ol>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->as_array(null, 'name')->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($array[0], true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->group_by('name')->as_array('id', 'name')->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($array[1], true) ?></pre>
            </li>
            <li>
                <h5>Query:</h5> <pre class="query">JSONDB::factory('users')->group_by('name')->as_array('name', 'id')->find_all();</pre>
                <h5>Result:</h5> <pre class="result"><?= print_r($array[2], true) ?></pre>
            </li>
        </ol>
    </div>
</div>