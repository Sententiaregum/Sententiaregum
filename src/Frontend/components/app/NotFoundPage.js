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
import Component from './Component';
import Translate from 'react-translate-component';
import { portal, network } from '../../config/menu';
import { ApiKey } from '../../util/http/facade/HttpServices';

/**
 * React component for 404 pages.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class NotFoundPage extends Component {
  /**
   * @inheritdoc
   */
  getMenuData() {
    return ApiKey.isLoggedIn() ? network : portal;
  }

  /**
   * @inheritdoc
   */
  renderPage() {
    return (
      <div>
        <h1>
          <Translate content="pages.not_found.title" />
        </h1>
        <div className="content">
          <Translate content="pages.not_found.text" />
        </div>
      </div>
    );
  }
}
