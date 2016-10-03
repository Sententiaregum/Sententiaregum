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
import DismissableAlertBox from '../../app/markup/DismissableAlertBox';
import Translate from 'react-translate-component';

/**
 * Component for name suggestions.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Suggestions extends Component {
  /**
   * Constructor.
   *
   * @param {Object} props Component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);
    this.state = {
      suggestions: props.suggestions
    };
  }

  /**
   * Property receiver for state refreshing.
   *
   * @param {Object} next New properties.
   *
   * @returns {void}
   */
  componentWillReceiveProps(next) {
    this.setState({ suggestions: next.suggestions });
  }

  /**
   * Render.
   *
   * @returns {React.Element} The markup.
   */
  render() {
    if (0 === this.props.suggestions.length || !this.props.suggestions) {
      return null;
    }
    return (
      <DismissableAlertBox bsStyle="warning">
        <p><Translate content="pages.portal.create_account.suggestions" /></p>
        <ul ref="list">
          {this.props.suggestions.map((suggestion, key) => <li key={key}>{suggestion}</li>)}
        </ul>
      </DismissableAlertBox>
    );
  }
}

Suggestions.propTypes = {
  suggestions: React.PropTypes.arrayOf(React.PropTypes.string)
};
