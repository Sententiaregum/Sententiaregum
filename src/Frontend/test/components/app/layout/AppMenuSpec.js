/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { shallow }  from 'enzyme';
import React        from 'react';
import AppMenu      from '../../../../components/app/layout/AppMenu';
import { expect }   from 'chai';

describe('AppMenuSpec', () => {
  it('renders the menu markup', () => {
    const items = [
      {
        label: 'Landing Page'
      },
      {
        label: 'Create Account'
      }
    ];

    const store = {
      subscribe() {},
      dispatch() {},
      getState() {
        return {
          menu: items
        };
      }
    };

    const markup = shallow((
      <AppMenu />
    ), { context: { store } });

    expect(markup.prop('items')).to.equal(items);
  });
});
