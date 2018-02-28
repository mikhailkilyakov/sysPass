<?php
/**
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Core\Events;

use SP\Html\Html;

/**
 * Class EventMessage
 * @package SP\Core\Events
 */
class EventMessage
{
    /**
     * @var array Detalles de la acción en formato "detalle : descripción"
     */
    protected $details = [];
    /**
     * @var int
     */
    protected $descriptionCounter = 0;
    /**
     * @var int
     */
    protected $detailsCounter = 0;
    /**
     * @var array
     */
    protected $description = [];

    /**
     * @return static
     */
    public static function factory()
    {
        return new static();
    }

    /**
     * Devuelve la descripción de la acción realizada en formato HTML
     *
     * @param bool $translate
     * @return string
     */
    public function getHtmlDescription($translate = false)
    {
        return nl2br($this->getDescription($translate));
    }

    /**
     * Devuelve la descripción de la acción realizada
     *
     * @param bool $translate
     * @return string
     */
    public function getDescription($translate = false)
    {
        if (count($this->description) === 0) {
            return '';
        }

        if (count($this->description) > 1) {
            if ($translate === true) {
                return implode(PHP_EOL, array_map('__', $this->description));
            }

            return implode(PHP_EOL, $this->description);
        }

        return $translate === true ? __($this->description[0]) : $this->description[0];
    }

    /**
     * Añadir detalle en formato HTML. Se resalta el texto clave.
     *
     * @param $key   string
     * @param $value string
     * @return $this
     */
    public function addDetailHtml($key, $value)
    {
        $this->addDetail(Html::strongText($key), $value);

        return $this;
    }

    /**
     * Establece los detalles de la acción realizada
     *
     * @param $key   string
     * @param $value string
     * @return $this
     */
    public function addDetail($key, $value)
    {
        if ($value === '' || $key === '') {
            return $this;
        }

        $this->details[] = [$this->formatString($key), $this->formatString($value)];

        $this->detailsCounter++;

        return $this;
    }

    /**
     * Formatear una cadena para guardarla en el registro
     *
     * @param $string string La cadena a formatear
     * @return string
     */
    private function formatString($string)
    {
        return strip_tags($string);
    }

    /**
     * Establece la descripción de la acción realizada en formato HTML
     *
     * @param string $description
     * @return $this
     */
    public function addDescriptionHtml($description = '')
    {
        $this->addDescription(Html::strongText($description));

        return $this;
    }

    /**
     * Establece la descripción de la acción realizada
     *
     * @param string $description
     * @return $this
     */
    public function addDescription($description = '')
    {
        $this->description[] = $this->formatString($description);

        return $this;
    }

    /**
     * Añadir una línea en blanco a la descripción
     */
    public function addDescriptionLine()
    {
        $this->descriptionCounter++;

        return $this;
    }

    /**
     * Componer un mensaje en formato texto
     *
     * @return string
     */
    public function composeText()
    {
        $message[] = $this->getDescription(true);
        $message[] = $this->getDetails(true);

        return implode(PHP_EOL, $message);
    }

    /**
     * Devuelve los detalles de la acción realizada
     *
     * @param bool $translate
     * @return string
     */
    public function getDetails($translate = false)
    {
        if (count($this->details) === 0) {
            return '';
        }

        if (count($this->details) > 1) {
            if ($translate === true) {
                return implode(PHP_EOL, array_map(function ($detail) use ($translate) {
                    return $this->formatDetail($detail, $translate);
                }, $this->details));
            }

            return implode(PHP_EOL, array_map([$this, 'formatDetail'], $this->details));
        }

        return $this->formatDetail($this->details[0], $translate);
    }

    /**
     * Devolver un detalle formateado
     *
     * @param array $detail
     * @param bool $translate
     * @return string
     */
    protected function formatDetail(array $detail, $translate = false)
    {
        if ($translate === true) {
            return sprintf('%s : %s', __($detail[0]), __($detail[1]));
        }

        return sprintf('%s : %s', $detail[0], $detail[1]);
    }

    /**
     * Componer un mensaje en formato HTML
     *
     * @return mixed
     */
    public function composeHtml()
    {
        $message[] = '<div class="log-message">';
        $message[] = '<p class="description">' . nl2br($this->getDescription(true)) . '</p>';
        $message[] = '<p class="details">' . nl2br($this->getDetails(true)) . '</p>';
        $message[] = '</div>';

        return implode('', $message);
    }

    /**
     * Devuelve los detalles en formato HTML
     *
     * @param bool $translate
     * @return string
     */
    public function getHtmlDetails($translate = false)
    {
        return nl2br($this->getDetails($translate));
    }

    /**
     * @return int
     */
    public function getDescriptionCounter()
    {
        return $this->descriptionCounter;
    }

    /**
     * @return int
     */
    public function getDetailsCounter()
    {
        return $this->detailsCounter;
    }
}