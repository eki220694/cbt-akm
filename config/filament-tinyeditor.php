<?php

return [
    "version" => [
        "tiny" => "8.0.2",
        "language" => [
            "version" => "25.8.4",
            "package" => "langs8",
        ],
        "licence_key" => env("TINY_LICENSE_KEY", "no-api-key"),
    ],
    "provider" => "cloud",

    // PENCEGAHAN: Set ke false agar sistem otomatisasi internal tidak merusak sinkronisasi tema
    "darkMode" => false,

    /** cutsom */
    "skins" => [
        "ui" => "oxide",
        "content" => "default",
    ],

    "profiles" => [
        "default" => [
            "plugins" =>
                "accordion autoresize codesample directionality advlist link image lists preview pagebreak searchreplace wordcount code fullscreen insertdatetime media table emoticons",

            "toolbar" =>
                "undo redo removeformat | fontfamily fontsize styles | bold italic underline | alignleft aligncenter alignright | numlist bullist | image link tiny_mce_wiris_formulaEditor table | wordcount fullscreen",

            "upload_directory" => null,

            "external_plugins" => [
                "tiny_mce_wiris" =>
                    "https://cdn.jsdelivr.net/npm/@wiris/mathtype-tinymce6@8.2.0/plugin.min.js",
            ],

            "extended_valid_elements" => "*[*]",

            "content_style" =>
                "body { font-family: sans-serif; font-size: 15px; transition: background-color 0.3s, color 0.3s; }",
        ],

        "simple" => [
            "plugins" => "autoresize directionality emoticons link wordcount",
            "toolbar" =>
                "removeformat | bold italic | numlist bullist | link emoticons",
            "upload_directory" => null,
        ],

        "minimal" => [
            "plugins" => "link wordcount",
            "toolbar" => "bold italic link numlist bullist",
            "upload_directory" => null,
        ],

        "full" => [
            "plugins" =>
                "accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons help",
            "toolbar" =>
                "undo redo removeformat | fontfamily fontsize styles | bold italic underline | alignleft aligncenter alignright | numlist bullist | image link anchor media codesample emoticons | preview wordcount fullscreen",
            "upload_directory" => null,
        ],
    ],

    "languages" => [],

    "extra" => [],
];
