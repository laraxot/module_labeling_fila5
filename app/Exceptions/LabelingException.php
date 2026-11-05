<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

/**
 * Base delle eccezioni di dominio del modulo: il messaggio e' gia' tradotto e si puo' mostrare
 * all'operatore (notifica Filament).
 */
abstract class LabelingException extends \RuntimeException {}
