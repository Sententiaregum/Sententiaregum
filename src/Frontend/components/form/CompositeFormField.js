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

import React, { Component } from 'react';
import FormGroup from 'react-bootstrap/lib/FormGroup';
import HelpBlock from 'react-bootstrap/lib/HelpBlock';
import FormHelper from '../../util/react/FormHelper';
import counterpart from 'counterpart';
import invariant from 'invariant';
import Locale from '../../util/http/Locale';

/**
 * Abstract ReactJS component which behaves as wrapper for form fields.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class CompositeFormField extends Component {
  /**
   * Constructor.
   *
   * @param {Object} props The object properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);
    this._change = () => this.forceUpdate();
  }

  /**
   * Lifecycle hook which establishes a connection between the event manager of the translator
   * and the form wrapper component.
   *
   * @returns {void}
   */
  componentDidMount() {
    counterpart.onLocaleChange(this._change);
  }

  /**
   * Lifecycle hook which closes the connection between the event manager of the translator and the wrapper.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    counterpart.offLocaleChange(this._change);
  }

  /**
   * Renderer which creates the form component.
   *
   * @returns {React.Element} The markup.
   */
  render() {
    const { name, errors, helper } = this.props;
    const messages                 = this._getArray(this._getErrorsForCurrentLanguage(errors, name));

    return (
      <FormGroup controlId={name} validationState={helper.associateFieldsWithStyle(messages)}>
        {React.Children.map(this.props.children, child => {
          if (child.props.placeholder) {
            return React.cloneElement(child, { placeholder: helper.getTranslatedFormField(child.props.placeholder) });
          }
          return child;
        })}
        {messages.map((error, i) => <HelpBlock key={i}>{error}</HelpBlock>)}
      </FormGroup>
    );
  }

  /**
   * Gets the errors for current language.
   *
   * @param {Object} errors   The errors.
   * @param {String} property The property.
   *
   * @returns {Array.<String>} The error list.
   * @private
   */
  _getErrorsForCurrentLanguage(errors, property) {
    const errorList = errors[property];
    if ('undefined' === typeof errorList) {
      return [];
    }

    const errorsForProperty = errorList[Locale.getLocale()];
    invariant(
      'undefined' !== typeof errorsForProperty,
      'Cannot extract errors from state!'
    );

    return errorsForProperty;
  }

  /**
   * Transforms the error list in order to use a common format during the rendering process.
   *
   * @param {*} errors The errors.
   *
   * @returns {Array.<String>} The transformed error list.
   * @private
   */
  _getArray(errors) {
    return 'undefined' === typeof errors ? [] : (Array.isArray(errors) ? errors : [errors]);
  }
}

CompositeFormField.propTypes = {
  name:     React.PropTypes.string,
  errors:   React.PropTypes.object,
  helper:   React.PropTypes.instanceOf(FormHelper),
  children: React.PropTypes.node
};
