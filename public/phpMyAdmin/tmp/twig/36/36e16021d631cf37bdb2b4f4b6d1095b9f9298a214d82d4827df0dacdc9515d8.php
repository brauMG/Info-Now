<?php

/* table/structure/move_columns_dialog.twig */
class __TwigTemplate_288eaddfdc6d53fa13e43eb9d562d3eff084e7af09f7ad3215183944a7171c9b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<div id=\"move_columns_dialog\" class=\"hide\" title=\"";
        echo _gettext("Move columns");
        echo "\">
    <p>";
        // line 2
        echo _gettext("Move the columns by dragging them up and down.");
        echo "</p>
    <form action=\"tbl_structure.php\">
        <div>
            ";
        // line 5
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null));
        echo "
            <ul></ul>
        </div>
    </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "table/structure/move_columns_dialog.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  30 => 5,  24 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "table/structure/move_columns_dialog.twig", "/home/grntnyte55vb/public_html/rapidoss/public/phpMyAdmin/templates/table/structure/move_columns_dialog.twig");
    }
}
