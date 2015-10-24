<?php
namespace Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Base\DB;

class RankingCommand extends Command
{

    /**
     *
     * @var string Command Name
     */
    protected $name = "ranking";

    /**
     *
     * @var string Command Description
     */
    protected $description = "Show the ranking of all users in this group";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // This will update the chat status to typing...
        $this->replyWithChatAction(Actions::TYPING);
        try {
            $db = DB::getInstance();
            $stmt = $db->perform("SELECT * FROM users;");
            while ($user = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $profile = json_decode(file_get_contents('https://www.duolingo.com/users/' . $user['username']));
                $data[($profile->fullname ?  : $profile->username)] = 0;
                foreach ($profile->languages as $lang) {
                    $data[($profile->fullname ?  : $profile->username)] += $lang->points;
                }
            }
            $tmp_filename = $this->getGraph($data);
            $this->replyWithPhoto($tmp_filename);
            unlink($tmp_filename);
        } catch (\Exception $e) {
            $this->replyWithMessage(print_r($data, true));
            $this->replyWithMessage('Fail in graphic generate');
        }
    }

    private function getGraph($values)
    {
        $total_bars = count($values);
        $max_value = max($values);
        $min_value = min($values);
        $gap = 10;
        $bar_width = 50;
        $margins = 20;
        $horizontal_lines = 15;
        $img_height = 300;
        $img_width = ($bar_width * $total_bars) + ($gap * ($total_bars + 1)) + ($margins * 2);
        
        // ---- Find the size of graph by substracting the size of borders
        $graph_width = $img_width - $margins * 2;
        $graph_height = $img_height - $margins * 2;
        $ratio = (($graph_height) / ($total_bars ==1?1:($max_value - $min_value)));
        
        $img = imagecreate($img_width, $img_height);
        
        // ------- Define Colors ----------------
        imagecolorallocate($img, 0, 0, 0); // background
        $bar_color = imagecolorallocate($img, 0, 64, 128);
        $value_color = imagecolorallocate($img, 0, 64, 128);
        $y_legend_color = imagecolorallocate($img, 0, 64, 128);
        $x_legend_color = imagecolorallocate($img, 255, 100, 100);
        $background_graph_color = imagecolorallocate($img, 240, 240, 255);
        $border_color = imagecolorallocate($img, 200, 200, 200);
        $horizontal_line_color = imagecolorallocate($img, 220, 220, 220);
        
        // create border of grath
        imagefilledrectangle($img, 1, 1, $img_width - 2, $img_height - 2, $border_color);
        // create background of graph
        imagefilledrectangle($img, $margins, $margins, $img_width - 1 - $margins, $img_height - 1 - $margins, $background_graph_color);
        
        // -------- Create scale and draw horizontal lines --------
        $horizontal_gap = $graph_height / $horizontal_lines;
        
        for ($i = 1; $i <= $horizontal_lines; $i ++) {
            $y = $img_height - $margins - $horizontal_gap * $i;
            // horizontal line
            imageline($img, $margins, $y, $img_width - $margins, $y, $horizontal_line_color);
            $v = intval($horizontal_gap * $i / ($graph_height / $max_value));
            // horizontal legend
            imagestring($img, 0, 5, $y - 5, $v, $bar_color);
        }
        
        // ----------- Draw the bars here ------
        for ($i = 0; $i < $total_bars; $i ++) {
            // ------ Extract key and value pair from the current pointer position
            list ($key, $value) = each($values);
            // col x pos ini
            $x1 = $margins + $gap + $i * ($gap + $bar_width);
            // col x pos end
            $x2 = $x1 + $bar_width;
            // Y position//intval($value * $ratio)
            $y1 = $margins + $graph_height - ($value - $min_value) * $ratio;
            // botton position
            $y2 = $img_height - $margins;
            // Values
            imagestring($img, 0, $x1 + ($bar_width / 2) - 6, $y1 - 10, $value, $value_color);
            // Bar
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $bar_color);
            // Bar names
            imagestringup($img, 5, $x1 + ($bar_width / 2) - 8, $img_height - ($margins + 5), $key, $x_legend_color);
        }
        $tmp_file = tempnam(sys_get_temp_dir(), 'FOO');
        rename($tmp_file, $tmp_file = $tmp_file.'.png');
        imagepng($img, $tmp_file);
        return $tmp_file;
    }
}