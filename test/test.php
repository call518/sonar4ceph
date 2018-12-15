<html>
</body>
<?php

include '../_config.php';
include '../_functions.php';

$jsonData = simple_curl("$ceph_api/osd/dump");
$arrOsdData = json_decode($jsonData, true)['output']['osds'];
//print_r($arrOsdData);

$osd_index = array_search('0', array_column($arrOsdData, 'osd'));
//echo $osd_index;

//print_r($arrOsdData[$osd_index]);

print_r(json2table($arrOsdData[$osd_index]));

function json2table($data)
{
    $table = '
    <table class="json-table" width="100%">
    ';
    foreach ($data as $key => $value) {
        $table .= '
        <tr valign="top">
        ';
        if ( ! is_numeric($key)) {
            $table .= '
            <td>
                <strong>'. $key .':</strong>
            </td>
            <td>
            ';
        } else {
            $table .= '
            <td colspan="2">
            ';
        }
        if (is_object($value) || is_array($value)) {
            $table .= json2table($value);
        } else {
            $table .= $value;
        }
        $table .= '
            </td>
        </tr>
        ';
    }
    $table .= '
    </table>
    ';
    return $table;
}
?>
</body>
</html>
