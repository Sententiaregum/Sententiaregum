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
import ReactPageComponentDecorator from '../../../components/app/ReactPageComponentDecorator';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('ReactPageComponentDecorator', () => {
  it('converts auth configuration', () => {
    const instance = new ReactPageComponentDecorator(
      {
        authConfig: {
          'isAdmin':    true,
          'isLoggedIn': true
        }
      }
    );

    instance.componentWillMount();
    expect(instance.authConfig.isLoggedIn).to.equal(true);
    expect(instance.authConfig.isAdmin).to.equal(true);
  });
});
