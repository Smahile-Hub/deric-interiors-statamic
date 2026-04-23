<?php

namespace App\Fieldtypes;

use Statamic\Fields\Fieldtype;

class TextStyle extends Fieldtype
{
    protected $icon = 'text-formatting-wrap-image-top';

    public $categories = ['controls'];

    protected $keywords = ['text', 'size', 'width', 'height', 'line', 'spacing', 'typography'];

    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return [
            'size' => 0,
            'height' => 0,
            'width' => 0,
        ];
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        $defaults = $this->defaultValue();

        if (! is_array($data)) {
            return $defaults;
        }

        return [
            'size' => (int) ($data['size'] ?? 0),
            'height' => (int) ($data['height'] ?? 0),
            'width' => (int) ($data['width'] ?? 0),
        ];
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return [
            'size' => (int) ($data['size'] ?? 0),
            'height' => (int) ($data['height'] ?? 0),
            'width' => (int) ($data['width'] ?? 0),
        ];
    }

    public function preload(): array
    {
        return [
            'defaults' => $this->defaultValue(),
            'limits' => [
                'size' => ['min' => -6, 'max' => 12, 'step' => 1],
                'height' => ['min' => -6, 'max' => 12, 'step' => 1],
                'width' => ['min' => -6, 'max' => 12, 'step' => 1],
            ],
            'labels' => [
                'size' => 'Text size',
                'height' => 'Line spacing',
                'width' => 'Text width',
            ],
        ];
    }

    public function preProcessIndex($data)
    {
        $data = $this->preProcess($data);

        if ($data['size'] === 0 && $data['height'] === 0 && $data['width'] === 0) {
            return 'Default';
        }

        return collect([
            $data['size'] ? 'Size '.($data['size'] > 0 ? '+' : '').$data['size'] : null,
            $data['height'] ? 'Spacing '.($data['height'] > 0 ? '+' : '').$data['height'] : null,
            $data['width'] ? 'Width '.($data['width'] > 0 ? '+' : '').$data['width'] : null,
        ])->filter()->implode(' | ');
    }
}
