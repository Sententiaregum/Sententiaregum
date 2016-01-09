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

import Component from './Component';
import React from 'react';

/**
 * Decorator which configures the internal page component class with its properties.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class ReactPageComponentDecorator extends Component {
  /**
   * This lifecycle callback fills the auth configuration
   * with the given auth props before component mount and rendering process.
   *
   * @returns {void}
   */
  componentWillMount() {
    for (const property of ['isLoggedIn', 'isAdmin']) {
      if ('undefined' !== typeof this.props.authConfig[property]) {
        this.authConfig[property] = this.props.authConfig[property];
      }
    }
  }

  /**
   * @inheritdoc
   */
  renderPage() {
    return this.props.app;
  }

  /**
   * @inheritdoc
   */
  getMenuData() {
    return this.props.menuData;
  }
}

ReactPageComponentDecorator.propTypes = {
  menuData:   React.PropTypes.array,
  app:        React.PropTypes.node,
  authConfig: React.PropTypes.object
};

ReactPageComponentDecorator.defaultProps = {
  authConfig: {},
  menuData:   []
};
