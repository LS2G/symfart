
<?php








use Twig\Environment;
use Twig\Node\CheckSecurityNode;
use Twig\Node\CheckToStringNode;
use Twig\Node\Expression\Binary\ConcatBinary;
use Twig\Node\Expression\Binary\RangeBinary;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\SetNode;
/**
 * @final
 */
 class SandboxNodeVisitor extends AbstractNodeVisitor
 {

    protected $inAModule = false;
    protected $tags;
    protected $filters;
    protected $functions;
    private $needsToStringWrap = false;
    protected function doEnterNode(Node $node, Environment $env)
    {
        if ($node instanceof ModuleNode) {

                $this->functions['range'] = $node;
            }
            
            if ($node instanceof PrintNode) {
                
                $this->needsToStringWrap = true;
                $this->wrapNode($node, 'expr');
            }
            if ($node instanceof SetNode && !$node->getAttribute('capture')) {
                $this->needsToStringWrap = true;
            }
            // wrap outer nodes that can implicitly call __toString()
            if ($this->needsToStringWrap) {
                if ($node instanceof ConcatBinary) {
                    $this->wrapNode($node, 'left');
                    $this->wrapNode($node, 'right');
                }
                if ($node instanceof FilterExpression) {
                    $this->wrapNode($node, 'node');
                    $this->wrapArrayNode($node, 'arguments');
                }
                if ($node instanceof FunctionExpression) {
                    $this->wrapArrayNode($node, 'arguments');
                }
            }
        }
 protected function doLeaveNode(Node $node, Environment $env){
         if($node instanceof ModuleNode) {  $this->inAModule = false;
            $node->setNode('constructor_end', new Node([new CheckSecurityNode($this->filters, $this->tags, $this->functions), $node->getNode('display_start')]));
        } elseif ($this->inAModule) {
            if ($node instanceof PrintNode || $node instanceof SetNode) {
                $this->needsToStringWrap = false;
            }
        }
        return $node;
    }
    private function wrapNode(Node $node, $name)
    {
        $expr = $node->getNode($name);
        if ($expr instanceof NameExpression || $expr instanceof GetAttrExpression) {
            $node->setNode($name, new CheckToStringNode($expr));
        }
    }
    private function wrapArrayNode(Node $node, $name)
    {
        $args = $node->getNode($name);
        foreach ($args as $name => $_) {
            $this->wrapNode($args, $name);
        }
    }
    public function getPriority()
    {
        return 0;
    }
}

class_alias('Twig\NodeVisitor\SandboxNodeVisitor', 'Twig_NodeVisitor_Sandbox');
?>