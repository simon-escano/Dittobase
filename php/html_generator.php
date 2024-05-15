<?php

class HTML {
    public $html;
    public function __construct(...$args) {
        $this->add(...$args);
    }

    public function add(...$args) {
        foreach ($args as $arg) {
            $this->html .= $arg;
        }
        return $this->html;
    }

    public function toString() {
        return $this->html;
    }
}

function html($elem, ...$args) {
    $attrs = [
        "id" => "",
        "class" => "",
        "style" => ""
    ];
    $contents = "";
    foreach ($args as $arg) {
        if (is_callable($arg)) $arg = $arg();
        if ($arg instanceof HTML) {
            $arg = $arg->toString();
        }
        if (is_array($arg)) {
            $attrs = array_merge($attrs, $arg);
            continue;
        }
        $first_char = substr($arg, 0, 1);
        $shifted_str = substr($arg, 1);
        switch ($first_char) {
            case "#":
                $attrs["id"] = $shifted_str;
                break;
            case ".":
                $attrs["class"] .= $shifted_str . " ";
                break;
            case "^":
                $attrs["style"] = $shifted_str;
                break;
            case "@":
                $attrs["src"] = $shifted_str;
                break;
            default:
                $contents .= $arg;
        }
    }

    $attr_str = "";
    foreach ($attrs as $key => $value) {
        if ($value) {
            $attr_str .= $key;
            $attr_str .= "='". $value ."'";
        }
    }

    return "<". $elem . " " . $attr_str . ">". $contents ."</". $elem .">";
}

function div(...$args) {
    return html("div", ...$args);
}

function section(...$args) {
    return html("section", ...$args);
}

function main(...$args) {
    return html("main", ...$args);
}

function head(...$args) {
    return html("header", ...$args);
}

function footer(...$args) {
    return html("footer", ...$args);
}

function nav(...$args) {
    return html("nav", ...$args);
}

function p(...$args) {
    return html("p", ...$args);
}

function span(...$args) {
    return html("span", ...$args);
}

function form($action, ...$args) {
    if ($action) {
        $action .= ".php";
    }
    return html("form", ["action" => $action, "method" => "post"], ...$args);
}

function label($for, ...$args) {
    return html("label", ["for" => $for], ...$args);
}

function select($name, ...$args) {
    $options = "";
    foreach ($args as $arg) {
        $options .= option($arg);
    }
    return html("select", ["name" => $name], $options, ...$args);
}

function option($value) {
    return html("option", ["value" => $value], $value);
}

function input($type, $name, $value, ...$args) {
    return html("input", ["type" => $type, "name" => $name, "value" => $value], ...$args);
}

function button($action, ...$args) {
    if (!$action) {
        return html("button", ...$args);
    }
    $unused_args = [];
    $hidden_inputs = "";
    foreach ($args as $arg) {
        if (is_array($arg)) {
            foreach ($arg as $name => $value) {
                $hidden_inputs .= input("hidden", $name, $value);
            }
        } else {
            $unused_args[] = $arg;
        }
    }
    return
    form($action,
        $hidden_inputs,
        html("button", ["name" => $action, "type" => "submit"], ...$unused_args)
    );
}

function img($src, ...$args) {
    return html("img", ["src" => $src], ...$args);
}

function hbox(...$args) {
    return div(".hbox", ...$args);
}

function vbox(...$args) {
    return div(".vbox", ...$args);
}

function card($img, $title, ...$args) {
    return
    div(".card",
        div(".card-header",
            div(".card-img-box",
                img("img/".$img)
            ),
            p(".card-title", $title)
        ),
        div(".card-content", ...$args)
    );
}

function pie_chart($total, ...$args) {
    $headers = "";
    $values = "";
    $colors = ["var(--secondary)", "var(--secondary-verylight)", "var(--primary)"];
    $totalColor = "var(--primary-dark) ";

    $headers .= div(".pie-chart-header",
        div(".pie-chart-header-percent", "^background-color: ". $totalColor,
            key($total), ": ", reset($total)
        )
    );

    $sum = 0;
    for ($i = 0; $i < count($args); $i++) {
        $arg = $args[$i];
        if (is_single_assoc_array($arg)) {
            $total_num = reset($total);
            $name = key($arg);
            $value = reset($arg);
            $color = $colors[$i % count($colors)] . " ";

            $headers .= 
            div(".pie-chart-header",
                div(".pie-chart-header-percent", "^background-color: " . $color,
                    percent($total_num, $value, true)
                ),
                p(".pie-chart-header-text", $name, ": ", $value)
            );
            $values .= $color . $sum . "% " . $sum + percent($total_num, $value) . "%, ";
            $sum += percent($total_num, $value);
            if ($i == count($args) - 1) {
                $values .= $totalColor . $sum . "% ";
            }
        }
    }
    
    return
    div(".pie-chart-container",
        div(".pie-chart-headers", $headers),
        div(".pie-chart", "^background: conic-gradient(". $values .")")
    );
}

function percent($total, $value, $string = false) {
    $percent = null;
    if (!$value) {
        $percent = 0;
    } else {
        $percent = ($value / $total) * 100;
    }
    if ($string) {
        $percent = intval($percent) . "%";
    }
    return $percent;
}

function is_single_assoc_array($array) {
    return is_array($array) && count($array) == 1 && is_string(key($array));
}

function barChartElem($percent, $color, $value) {
    return "
    <div class='bar-chart-elem'
        style='height: ". $percent ."; background-color: ". $color ."'
    >". $value ."</div>
    ";
}

function barChartLegend($name, $color) {
    return "
    <div class='bar-chart-legend-color'
        style='background-color: ". $color ."'
    ></div>" . p("bar-chart-legend-title", $name);
}

?>