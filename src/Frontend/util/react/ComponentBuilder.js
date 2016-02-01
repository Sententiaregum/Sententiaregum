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

import ReactPageComponentDecorator from '../../components/app/ReactPageComponentDecorator';
import React from 'react';

/**
 * Builder for reactjs page components.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ComponentBuilder {
  /**
   * Builder for page components.
   *
   * @param {Object} prototype Prototype to render into the decorator.
   * @param {Array} menuData Menu configuration.
   * @param {Object} authData Auth configuration.
   *
   * @returns {Function} React component.
   */
  buildGenericComponentForPage(prototype, menuData, authData) {
    return props => {
      const internalProperties = {
        app: prototype,
        menuData,
        authData
      };

      if ('undefined' !== typeof props.params) {
        internalProperties.params = props.params;
      }

      return React.createElement(
        ReactPageComponentDecorator,
        internalProperties
      );
    };
  }
}

export default new ComponentBuilder();
