<?php

namespace mdref\Generator;

use mdref\Generator;
use phpDocumentor\Reflection\{DocBlock, DocBlock\Tags, DocBlockFactory};

class Func extends Scrap {
	public function __toString() : string {
		return parent::toString(__FILE__, __COMPILER_HALT_OFFSET__);
	}
}

/** @var $gen Generator */
/** @var $ref \ReflectionFunctionAbstract */
/** @var $doc DocBlock */
/** @var $patch callable as function(string, \Reflector) */

__HALT_COMPILER();
# <?php
if ($ref instanceof \ReflectionMethod) :
	if ($ref->isFinal()) :
		?>final <?php
	endif;
	if ($ref->isStatic()) :
		?>static <?php
	endif;
endif;
?><?= $ref->hasReturnType() ? $ref->getReturnType() : "void"
?> <?php
if ($ref instanceof \ReflectionMethod) :
	?><?=$ref->getDeclaringClass()->getName()
	?>::<?php
endif;
?><?= $ref->getName() ?>
(<?php
	$optional = 0;
	foreach ($ref->getParameters() as $i => $param) :
		if ($param->isOptional()) : $optional++
			?>[<?php
		endif;
		$patch(Arg::class, $param);
		if ($i < $ref->getNumberOfParameters()-1):
			?>, <?php
		endif;
	endforeach;
	echo str_repeat("]", $optional);
?>
)

<?= $doc?->getSummary() ?>


<?= $doc?->getDescription()?->getBodyTemplate() ?>


<?php $patch(SeeAlso::class, $ref) ?>



## Params:

<?php
if (!($params = $ref->getParameters())) :
	?>None.<?php
else :
	foreach ($params as $i => $param) :
		$patch(Param::class, $param);
	endforeach;
endif;

if (($tags = $doc?->getTagsWithTypeByName("return")) || ($ref->hasReturnType() && $ref->hasReturnType() != "void")) :
?>


## Returns:

<?php
	if ($tags) :
		foreach ($tags as $tag) :
			?>* <?= $tag->getType()
			?>, <?= $tag->getDescription()
			?><?="\n"
			?><?php
		endforeach;
	else :
		?>* <?= $ref->getReturnType()
		?><?php
	endif;
endif;
?>


<?php
if (($tags = $doc?->getTagsWithTypeByName("throws"))) :
?>

## Throws:

<?php
	foreach ($tags as $tag) :
		?>* <?= $tag->getType()
		?><?php
		if ($tag->getDescription()?->getBodyTemplate()) :
			?>, <?= $tag->getDescription()
			?><?php
		endif;
		?><?="\n"
	?><?php
	endforeach;
endif;
?>


<?php
