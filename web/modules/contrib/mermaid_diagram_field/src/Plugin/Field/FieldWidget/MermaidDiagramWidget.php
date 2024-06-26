<?php

namespace Drupal\mermaid_diagram_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A Mermaid diagram widget.
 *
 * @FieldWidget(
 *   id = "mermaid_diagram_widget",
 *   label = @Translation("Mermaid diagram widget"),
 *   field_types = {
 *     "mermaid_diagram",
 *   }
 * )
 */
class MermaidDiagramWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['title'] = [
      '#type' => 'textfield',
      '#size' => 60,
      '#title' => $this->t('Title'),
      '#default_value' => $items[$delta]->title ?? NULL,

    ];
//@todo workout required states.
    $element['diagram'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mermaid code'),
      '#description' => t('Must be valid Mermaid code. <a href="@mermaid-live" target="_blank">Mermaid Live editor (opens in new tab)</a>.', array('@mermaid-live' => 'https://mermaid.live/')),
      '#default_value' => $items[$delta]->diagram ?? NULL,
    ];
    $element['key'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Key'),
      '#description' => t('Must be valid Mermaid code. <a href="@mermaid-live" target="_blank">Mermaid Live editor (opens in new tab)</a>.', array('@mermaid-live' => 'https://mermaid.live/')),
      '#default_value' => $items[$delta]->key ?? NULL,
    ];
    $element['caption'] = [
      '#type' => 'textarea',
      '#title' => $this->t('The caption used to describe the diagram.'),
      '#default_value' => $items[$delta]->caption ?? NULL,
    ];

    $element['show_code'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show the raw Mermaid code.'),
      '#default_value' => $items[$delta]->show_code ?? NULL,
    ];

    return $element;
  }

}
