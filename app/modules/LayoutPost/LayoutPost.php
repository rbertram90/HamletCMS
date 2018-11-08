<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\JSONHelper;

class LayoutPost
{
    public function onViewEditPost($args) {
        if ($args['type'] != 'layout') return;

        $controller = new LayoutPost\controller\LayoutPost();
        $controller->edit();
    }

    public function runTemplate($args)
    {
        $post = $args['post'];

        if ($args['template'] == 'postTeaser' && $post['type'] == 'layout') {
            $layout = JSONHelper::JSONtoArray($post['content']);
            $args['post']['trimmedContent'] = $this->generateLayoutMarkup($layout);
        }
    }

    /**
     * generateLayoutMarkup
     */
    protected function generateLayoutMarkup($array)
    {
        $out = "<div class='ui grid'>";

        foreach ($array['rows'] as $row) {
            $rowClasses = "";
            $rOut = "";
            $columnWidths = null;

            switch ($row['columnLayout']) {
                case "twoColumns_50":
                    $rowClasses = "two column";
                    break;

                case "twoColumns_75":
                    $columnWidths = [75, 25];
                    break;

                case "twoColumns_75":
                    $columnWidths = [25, 75];
                    break;

                case "twoColumns_66":
                    $columnWidths = [66, 33];
                    break;

                case "twoColumns_66":
                    $columnWidths = [33, 66];
                    break;

                case "threeColumns":
                    $rowClasses = "three column";
                    break;

                case "fourColumns":
                    $rowClasses = "four column";
                    break;

                default:
                case "singleColumn":
                    $columnWidths = [100];
                    break;
            }

            foreach ($row['columns'] as $c => $column) {

                $classes = "";
                if ($columnWidths) {
                    switch ($columnWidths[$c]) {
                        case 100: $classes = "sixteen wide"; break;
                        case 75: $classes = "twelve wide"; break;
                        case 66: $classes = "ten wide"; break;
                        case 33: $classes = "six wide"; break;
                        case 25: $classes = "four wide"; break;
                    }
                }

                $style = '';
                if (isset($column['backgroundColour'])) {
                    $classes .= ' ' . $column['backgroundColour'];
                }

                if (isset($column['fontColour'])) {
                    $style .= sprintf('color: %s;', $column['fontColour']);
                }

                if ($column['image']) {
                    $classes .= ' black image-column';
                    $style.= 'background-image: url('. $this->fileDir .'/'. $column['image'] .');';
                }
                if ($column['minimumHeight']) {
                    $style.= 'min-height: '. $column['minimumHeight'] .';';
                }

                $rOut.= sprintf("<div class='%s column' style='%s'>", $classes, $style);

                if ($column['textContent']) {
                    $rOut.= nl2br($column['textContent']);
                }

                $rOut.= "</div>";
            }

            $out .= sprintf("<div class='%s row'>%s</div>", $rowClasses, $rOut);
        }

        $out .= '</div>';

        return $out;
    }

}