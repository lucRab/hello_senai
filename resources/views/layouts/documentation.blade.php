<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page')</title>
    <link rel="stylesheet" href="/css/root.css">
    <link rel="stylesheet" href="/css/documentation.css">
</head>
<body>
    <!-- topo -->
    <header class="grid_doc" id="doc_header">
        HEADER 
    </header>
    <!-- /topo -->

    <!-- lateral -->
    <aside class="grid_doc" id="doc_aside">

         <!-- navegação -->
        <nav id="nav_doc">
            <button class="nav_button" id="btn_inicio" onclick="hide(this.id)">Inicio</button>
            <button class="nav_button" id="btn_usuario" onclick="hide(this.id)">Usuário</button>
            <button class="nav_button" id="btn_convite" onclick="hide(this.id)">Convite</button>
            <button class="nav_button" id="btn_projeto" onclick="hide(this.id)">Projeto</button>
        </nav>
        <!-- /navegação -->
    </aside>
    <!-- /lateral -->

    <!-- conteudo -->
    <main class="grid_doc" id="doc_main">
        <!-- Informações -->
        <section>
        @yield('infomation')
        </section>
        <!-- /Informações -->
    </main>
    <!-- /conteudo -->

    <!-- rodapé -->
    <footer class="grid_doc" id="doc_footer">
        FOOTER
    </footer>
    <!-- /rodapé -->
</body>
</html>
<script src="/js/navDocumentation.js"></script>