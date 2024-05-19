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
    $attr_str = "";
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
            case "!":
                $attr_str .= "required ";
                break;
            default:
                $contents .= $arg;
        }
    }

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

function textarea($type, $name, $value, ...$args) {
    return html("textarea", ["type" => $type, "name" => $name, "value" => $value], ...$args);
}

function button($action, ...$args) {
    if ($action == " ") {
        return html("button", ["type" => "submit"], ...$args);
    }
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
    if ($action == ".") {
        $action = "";
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

function generatePokedexResult($pokemon) {
    return div(".pokedex-result",
        div(".pokedex-header", 
            p(".pokedex-name", $pokemon['name']),
            hbox('.non-flex',
                p(".pokedex-type", $pokemon['type1']),
                $pokemon['type2'] != "None" ? p(".pokedex-type", $pokemon['type2']) : ""
            )
        ),
        div(".pokedex-body",
            img($pokemon['image'], ".pokedex-image"),
            p(".pokedex-description", $pokemon['description'])
        ),
        div(".pokedex-moves",
            $pokemon['name'], "'s moves",
            hbox(
                p(".pokedex-type", $pokemon['move1']),
                p(".pokedex-type", $pokemon['move2']),
                p(".pokedex-type", $pokemon['move3']),
            )
        )
    );
}

function generateEditPokedexResult($pokemon) {
    return form("edit_pokemon", ".pokedex-result",
        div(".pokedex-header",
            input("text", "name", $pokemon['name'], ".pokedex-name", "!"),
            hbox('.non-flex',
                input("text", "type1", $pokemon['type1'], ".pokedex-type", "!"),
                input("text", "type2", $pokemon['type2'], ".pokedex-type", "!")
            )
        ),
        div(".pokedex-body",
            img($pokemon['image'], ".pokedex-image"),
            textarea("text", "description", $pokemon['description'], ".pokedex-description", $pokemon['description'], "!"),
        ),
        div(".pokedex-moves",
            $pokemon['name'], "'s moves",
            hbox(
                input("text", "move1", $pokemon['move1'], ".pokedex-type", "!"),
                input("text", "move2", $pokemon['move2'], ".pokedex-type", "!"),
                input("text", "move3", $pokemon['move3'], ".pokedex-type", "!")
            )
        ),
        div(".pokedex-result-buttons",
            button(" ", "UPDATE", ["name" => "pokedexID", "value" => $pokemon["pokedexID"]], ".pokedex-update-btn"),
            button(" ", "DELETE", ["name" => "delete", "value" => "true"], ".pokedex-delete-btn")
        )
    );
}

function generateBlankPokedexResult() {
    return form("create_pokemon", ".pokedex-result", ".blank-pokedex-result",
        div(".pokedex-header",
            input("text", "name", "", ".pokedex-name", "!", ["placeholder" => "Name"]),
            hbox('.non-flex',
                input("text", "type1", "", ".pokedex-type", "!", ["placeholder" => "Type 1"]),
                input("text", "type2", "", ".pokedex-type", "!", ["placeholder" => "Type 2"])
            )
        ),
        div(".pokedex-body",
            div(".pokedex-body-left",
                input("text", "isStarter", "", ".pokedex-is-starter", ["placeholder" => "Starter?"]),
                textarea("text", "image", "", ".pokedex-image-src", "!", ["placeholder" => "Image Link"])
            ),
            div(".pokedex-body-right",
                input("text", "region", "", ".pokedex-region", ["placeholder" => "Region"]),
                textarea("text", "description", "", ".pokedex-description", "!", ["placeholder" => "Description"])
            )
        ),
        div(".pokedex-moves",
            hbox(
                input("text", "move1", "", ".pokedex-type", "!", ["placeholder" => "Move 1"]),
                input("text", "move2", "", ".pokedex-type", "!", ["placeholder" => "Move 2"]),
                input("text", "move3", "", ".pokedex-type", "!", ["placeholder" => "Move 3"])
            )
        ),
        div(".pokedex-result-buttons",
            button(" ", "CREATE", ["name" => "create"], ".pokedex-create-btn"),
        )
    );
}

function generatePokedex($pokedex, $content, ...$args) {
    $create = false;
    foreach ($args as $arg) {
        $first_char = substr($arg, 0, 1);
        if ($first_char == "+") {
            $create = true;
        }
    }
    if (is_callable($content)) {
        $content = $content();
    }
    return div("#pokedex", ".pokedex",
        div(".pokedex-top",
            div(".pokedex-top-left",
                div(".pokedex-lens",
                    div(".pokedex-lens-glass")
                ),
                div(".pokedex-top-button red"),
                div(".pokedex-top-button yellow"),
                div(".pokedex-top-button green")
            ),
            div(".pokedex-top-right",
                div(".pokedex-top-right-box",
                    p("", "POKEDEX")
                )
            )
        ),
        div(".pokedex-bottom",
            div(".pokedex-screen",
                div(".pokedex-screen-content",
                    $content
                ),
                div(".pokedex-screen-buttons",
                    div(".pokedex-screen-button"),
                    ($create) ? button(".", ["create" => ""], "Create", ".pokedex-create-btn") : "",
                    div(".pokedex-speaker",
                        "<hr>", "<hr>", "<hr>", "<hr>",
                    )
                )
            ),
            div(".pokedex-buttons",
                button("decrement_pokedex", ".pokedex-button", "<", ["page" => getPage()]),
                div(".pokedex-button-group",
                    div(".pokedex-line-buttons",
                        div(".pokedex-line-button red"),
                        div(".pokedex-line-button blue"),
                    ),
                    div(".pokedex-trackpad",
                        form("",
                            input("text", "search", null, ".pokedex-input", ["placeholder" => "Search"], "!"),
                            div(".pokedex-search-buttons",
                                button(" ", ["name" => "query", "value" => "name"], "Search by name"),
                                button(" ", ["name" => "query", "value" => "types"], "Search by types"),
                                button(" ", ["name" => "query", "value" => "moves"], "Search by moves")
                            )
                        )
                    )
                ),
                button("increment_pokedex", ".pokedex-button", ">", ["limit" => count($pokedex) - 1, "page" => getPage()])
            )
        )
    );
}

?>