/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React from 'react';
import translator from 'counterpart';

/**
 * FormComponent.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * @abstract
 */
export default class FormComponent extends React.Component {
  /**
   * Constructor.
   *
   * @returns {void}
   */
  constructor() {
    super();
    this.stateEnforcementHandler = () => this.forceUpdate();
  }

  /**
   * Registers the translator hook right after rendering.
   *
   * @returns {void}
   */
  componentDidMount() {
    translator.onLocaleChange(this.stateEnforcementHandler);
  }

  /**
   * Removes the hook when the component is about to be destroyed.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    translator.offLocaleChange(this.stateEnforcementHandler);
  }

  /**
   * Hook to change property.
   *
   * @param {Object} e Event object.
   *
   * @returns {void}
   */
  changeProperty(e) {
    const name = e.target.getAttribute('name'),
        patch  = this.state.data;

    patch[name] = e.target.value;
    this.setState({ data: patch });
  }

  /**
   * Builds a stack of translation components.
   *
   * @return {Object.<string>} List of translations.
   */
  _buildTranslationComponents() {
    const list       = {},
        fields       = this._getFormFields(),
        translations = fields.map(field => translator.translate(`${this._getTranslationPrefix()}.${field}`));

    for (let i = 0; i < translations.length; i++) {
      list[fields[i]] = translations[i];
    }

    return list;
  }

  /**
   * Builds a list of bootstrap styles for form components.
   *
   * @param {Array.<string>} additions Additional values.
   * @param {Array.<string>} removals  Removal of items.
   *
   * @returns {Object.<string>} Object list.
   */
  _getBootstrapStyles(additions, removals) {
    let fields = this._getFormFields();
    const list = {};
    fields     = fields.filter(item => -1 === removals.indexOf(item)).concat(additions);

    for (let i = 0; i < fields.length; i++) {
      list[`${fields[i]}Style`] = 'undefined' === typeof this.state.validation.errors[fields[i]]
        ? this.state.validation.submitted ? 'success' : null
        : 'error';
    }

    return list;
  }

  /**
   * Render errors.
   *
   * @returns {Object.<React.Element>} Array of react elements.
   */
  _renderErrors() {
    const result = {};
    for (const key in this.state.validation.errors) {
      if (!this.state.validation.errors.hasOwnProperty(key)) {
        continue;
      }
      result[key] = (
        <div>
          {this.state.validation.errors[key].map((message, key) => <p key={key}><span className="help-text">{message}</span></p>)}
        </div>
      );
    }

    return result;
  }

  /**
   * Getter for fields of a form.
   *
   * @returns {Array.<string>} Form fields.
   */
  _getFormFields() {
    throw new Error('This must be overriden to provide all form fields!');
  }

  /**
   * Getter for the translation prefix.
   *
   * @returns {string|null} The prefix.
   */
  _getTranslationPrefix() {
    return null;
  }
}
