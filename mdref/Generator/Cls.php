<?php

namespace mdref\Generator;

use mdref\Generator;
use phpDocumentor\Reflection\DocBlock;

class Cls extends Scrap {
	public function __toString() :string {
		return parent::toString(__FILE__, __COMPILER_HALT_OFFSET__);
	}
}

/** @var $gen Generator */
/** @var $ref \ReflectionClass */
/** @var $doc DocBlock */
/** @var $patch callable as function(string, \Reflector) */

__HALT_COMPILER();
# <?php
if ($ref instanceof \ReflectionEnum) :
	?>enum<?php
else :
	?><?= implode(" ", \Reflection::getModifierNames($ref->getModifiers()));
	?> <?= $ref->isInterface() ? "interface" : "class"
	?><?php
endif;

?> <?= $ref->getName() ?><?php

if (($parent = $ref->getParentClass())) :
	?> extends <?= $parent->getName() ?><?php
endif;
if (($implements = $ref->getInterfaceNames())) :
	foreach ($implements as $index => $iface) :
		foreach ($implements as $implemented) :
			if ($iface !== $implemented && is_subclass_of($implemented, $iface)) :
				unset($implements[$index]);
			endif;
		endforeach;
	endforeach;
	sort($implements);
	?> implements <?= implode(", ", $implements); ?><?php
endif;
?>


<?= $doc?->getSummary() ?>


<?= $doc?->getDescription() ?>


<?php $patch(SeeAlso::class, $ref) ?>



## Constants:

<?php
if (!($consts = array_filter($ref->getReflectionConstants(), fn($rc) => $rc->getDeclaringClass()->getName() === $ref->getName()))) :
	?>None.<?php
else:
	/** @var \ReflectionClassConstant $rc */
	foreach ($consts as $rc) :
		?> * <span class="constant"><?= $rc->getName();
		?></span> = <span><?php
		if ($rc->getValue() instanceof \UnitEnum) :
			var_export($rc->getValue()->value);
		else :
			var_export($rc->getValue());
		endif;
		?><?= "</span>\n"
		?><?php
	endforeach;
endif;
?>


## Properties:

<?php
if (!($props = array_filter($ref->getProperties(), fn($rp) => $rp->getDeclaringClass()->getName() === $ref->getName()))) :
	?>None.<?php
else:
	foreach ($props as $rp) :
		?> * <?php
		$patch(Prop::class, $rp);
	endforeach;
endif;
?>

<?php
