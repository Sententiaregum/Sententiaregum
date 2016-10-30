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
import DismissableAlertBox from './DismissableAlertBox';
import Locale from '../../../util/http/Locale';
import counterpart from 'counterpart';

/**
 * Component for an alert box containing one error from an error object with multiple translations.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class SimpleErrorAlert extends Component {
  /**
   * Constructor.
   *
   * @param {Object} props The object properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this.state = { errors: props.error };

    this.refreshTranslations = () => this.forceUpdate();
  }

  /**
   * Lifecycle callback which adds the translation hook.
   *
   * @returns {void}
   */
  componentDidMount() {
    counterpart.onLocaleChange(this.refreshTranslations);
  }

  /**
   * Lifecycle callback which removes the translation hook.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    counterpart.offLocaleChange(this.refreshTranslations);
  }

  componentWillReceiveProps(next) {
    this.setState({ errors: next.error });
  }

  /**
   * Builds the markup.
   *
   * @returns {React.Element} The markup.
   */
  render() {
    return (
      <DismissableAlertBox bsStyle="danger">
        <p>{this.state.errors[Locale.getLocale()]}</p>
      </DismissableAlertBox>
    );
  }
}

SimpleErrorAlert.propTypes = {
  error: React.PropTypes.object
};
