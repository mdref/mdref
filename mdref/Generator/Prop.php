<?php

namespace mdref\Generator;

use mdref\Generator;
use phpDocumentor\Reflection\{DocBlock, DocBlock\Tags};

class Prop extends Scrap {
	public function __toString() : string {
		return parent::toString(__FILE__, __COMPILER_HALT_OFFSET__, [
			"tag" => $this->getVarTag($this->ref->getName())
		]);
	}

}

/** @var $gen Generator */
/** @var $ref \ReflectionParameter */
/** @var $doc ?DocBlock */
/** @var $tag ?Tags\Param */

__HALT_COMPILER();
<?= implode(" ", \Reflection::getModifierNames($ref->getModifiers()))
?> <?= $ref->hasType() ? $ref->getType() : ($tag?->getType() ?? "mixed")
?> $<?=$ref->getName() ?><?php
if ($ref->hasDefaultValue()) :
	?> = <?php var_export($ref->getDefaultValue()) ?><?php
endif;

if (($desc = $doc?->getSummary())) :
	?><?= "  \n  $desc"
	?><?php
endif;

?><?= "\n"
?><?php

