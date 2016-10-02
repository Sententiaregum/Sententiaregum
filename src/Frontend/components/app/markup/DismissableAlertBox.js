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
import Alert from 'react-bootstrap/lib/Alert';

/**
 * Alert box based on react-bootstrap which is able to be removed.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class DismissableAlertBox extends Component {
  /**
   * Constructor.
   *
   * @param {Array} props Internal react properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);
    this.state = {
      toggled: true
    };
  }

  /**
   * Renders the dismissable alert.
   *
   * @returns {React.Element} The react dom markup.
   */
  render() {
    if (this.state.toggled) {
      return (
        <Alert bsStyle={this.props.bsStyle} onDismiss={this._toggle.bind(this)}>
          {this.props.children}
        </Alert>
      );
    }

    return false;
  }

  /**
   * Modifies the toggle flag and causes a re-render.
   *
   * @returns {void}
   *
   * @private
   */
  _toggle() {
    this.setState({
      toggled: !this.state.toggled
    });
  }
}

DismissableAlertBox.propTypes = {
  bsStyle: React.PropTypes.string
};
