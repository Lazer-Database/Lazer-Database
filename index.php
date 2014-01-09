<!DOCTYPE HTML>
<html>

<head>
    <title>Lazer Database Examples</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600" rel="stylesheet" type="text/css" />
    <link href="http://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="http://yandex.st/highlightjs/7.5/styles/tomorrow.min.css">
    <script src="http://yandex.st/highlightjs/7.5/highlight.min.js"></script>
    <style>
        /* Eric Meyer's Reset CSS v2.0 - http://cssreset.com */
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{border:0;font-size:100%;font:inherit;vertical-align:baseline;margin:0;padding:0}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:none}table{border-collapse:collapse;border-spacing:0} 
        /* Styles */
html { height: 100%; width: 100%; } body { font-family: Source Sans Pro; font-size: 20px; font-weight: 300; color: #373737; background: #373737; display:table; width: 100%; height: 100%; table-layout:fixed; } @media screen and (min-width: 800px) and (min-height: 500px) { #leftblock { width: 270px; height: 100%; display:table-cell; vertical-align: top; } #leftblock > div { width: 270px; position: fixed; height: 100%; } } @media screen and (max-width: 800px) { #leftblock { display: none; } } @media screen and (max-height: 500px) { #leftblock { width: 270px; height: 100%; display:table-cell; vertical-align: top; } #leftblock > div { width: 270px; height: 100%; } } @media screen and (max-width: 800px) and (max-height: 500px) { #leftblock { display: none; } } #content { padding: 20px 40px; background: white; display:table-cell; } h1 { color: #fff; font-size: 34px; font-weight: 200; margin-bottom: 40px; padding: 20px; } h2 { font-size: 32px; font-weight: 300; margin: 10px 0; color: #000; margin-left: -10px; } h3 { padding: 10px 5px; font-size: 17px; } p { padding: 5px; } p strong { font-weight: 600; color: #FF5500; } a { color: #000; text-decoration: none; } nav > ul li { line-height: 2.5em; text-align: right; } nav > ul li a { display: block; color: #fff; padding-right: 50px; } nav > ul li:hover { background: #222729; } hr { margin: 20px auto; border:0; height: 1px; background: #f0f0f0; width: 100%; } code { display: block; margin: 10px 25px !important; padding: 10px 20px !important; font-family: Inconsolata; font-size: 18px; background: #f9f9f9 !important; color: #373737 !important; border-left: 1px dashed #878787; word-wrap: break-word; } samp { display: block; margin: 0 25px; padding: 10px 20px; font-family: Inconsolata; font-size: 18px; background: #f9f9f9; border-left: 1px dashed #878787; } </style>
    <script>hljs.initHighlightingOnLoad();</script>
</head>

<body>
    <div id="leftblock">
        <div>
            <h1>Lazer Database</h1>
            <nav>
                <ul>
                    <li>
                        <a href="#findAll">Find All</a>
                    </li>
                    <li>
                        <a href="#limit">Limit</a>
                    </li>
                    <li>
                        <a href="#orderBy">Order By</a>
                    </li>
                    <li>
                        <a href="#where">Where</a>
                    </li>
                    <li>
                        <a href="#groupBy">Group By</a>
                    </li>
                    <li>
                        <a href="#count">Count</a>
                    </li>   
                    <li>
                        <a href="#asArray">As Array</a>
                    </li>
                    <li>
                        <a href="#with">With (JOIN)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="content">

        <h2>Welcome</h2>
        <p>Hi, on this page you will find some example of use my database.</p>
        <hr />

        <h2 id="findAll">Find All</h2>
        <h3>Query:</h3>
        <pre><code>$result = Lazer::table('users')-&gt;findAll();
foreach($result as $row)
{
    print_r($row);
}</code></pre>
        <hr />
        
        <h2 id="limit">Limit</h2>
        <h3>Query:</h3>
        <pre><code>Lazer::table('users')-&gt;limit(5)-&gt;findAll(); /* Get five records */
Lazer::table('users')-&gt;limit(10, 5)-&gt;findAll(); /* Get five records from 10th */</code></pre>
        <hr />

        <h2 id="orderBy">Order By</h2>
        <h3>Query:</h3>
        <pre><code>Lazer::table('users')-&gt;orderBy('id')-&gt;findAll();
Lazer::table('users')-&gt;orderBy('id', 'DESC')-&gt;findAll();
Lazer::table('users')-&gt;orderBy('id')-&gt;orderBy('name')-&gt;findAll();</code></pre>
        <hr />
        
        <h2 id="where">Where</h2>
        <h3>Query:</h3>
        <pre><code>Lazer::table('users')-&gt;where('id', '=', 1)-&gt;findAll();
Lazer::table('users')-&gt;where('id', '&gt;', 4)-&gt;findAll();
Lazer::table('users')-&gt;where('id', 'IN', array(1, 3, 6, 7))-&gt;findAll();
Lazer::table('users')-&gt;where('id', '&gt;=', 2)-&gt;andWhere('id', '&lt;=', 7)-&gt;findAll();
Lazer::table('users')-&gt;where('id', '=', 1)-&gt;orWhere('id', '=', 3)-&gt;findAll();</code></pre>
        <hr />
        
        <h2 id="groupBy">Group By</h2>
        <h3>Query:</h3>
        <pre><code>Lazer::table('news')-&gt;groupBy('category_id')-&gt;findAll();</code></pre>
        <hr />
        
         <h2 id="count">Count</h2>
        <h3>Query:</h3>
        <pre><code>Lazer::table('users')-&gt;count(); /* Number of rows */

Lazer::table('users')-&gt;findAll()-&gt;count(); /* Number of rows */

$users = Lazer::table('users')-&gt;findAll();
count($users); /* Number of rows */</code></pre>
        <p>You can use it with rest of methods</p>
          <h3>Query:</h3>
        <pre><code>Lazer::table('news')-&gt;where('id', '=', 2)-&gt;count();
Lazer::table('news')-&gt;groupBy('category_id')-&gt;count();</code></pre>
        <hr />

        <h2 id="asArray">As Array</h2>
        Use when you want to get array with results, not an object to iterate.
        <h3>Query:</h3>
        <pre><code>Lazer::table('users')-&gt;findAll()-&gt;asArray();
Lazer::table('users')-&gt;findAll()-&gt;asArray('id'); /* key of row will be an ID */
Lazer::table('users')-&gt;findAll()-&gt;asArray(null, 'id'); /* value of row will be an ID */
Lazer::table('users')-&gt;findAll()-&gt;asArray('id', 'name'); /* key of row will be an ID and value will be a name of user */</code></pre>
        <hr />
        
        <h2 id="with">With (JOIN)</h2>
        <p><strong>Caution! First letter of relationed table name is always uppercase.</strong></p>
        <p>For example you can get News with it Comments. </p>
        <h3>Query:</h3>
        <pre><code>$news = Lazer::table('news')-&gt;with('comments')-&gt;findAll();
foreach($news as $post)
{
    print_r($post);

    $comments = $post-&gt;Comments-&gt;findAll();
    foreach($comments as $comment)
    {
        print_r($comment);
    }
}</code></pre>
                <p>Also you can get News with it Author, Comments and each comment with it author</p>
        <h3>Query:</h3>
        <pre><code>$news = Lazer::table('news')-&gt;with('users')-&gt;with('comments')-&gt;with('comments:users')-&gt;findAll();
foreach($news as $post)
{
    print_r($post-&gt;Users-&gt;name); /* news author name */

    $comments = $post-&gt;Comments-&gt;findAll(); /* news comments */
    foreach($comments as $comment)
    {
        print_r($comment-&gt;Users-&gt;name); /* comment author name */
    }
}</code></pre>
        <p>In queries you can use all of features, simple example</p>
        <pre><code>$post-&gt;Comments-&gt;orderBy('author_id')-&gt;limit(5)-&gt;findAll(); /* news comments */</code></pre>
        <hr />
    </div>

</body>

</html>